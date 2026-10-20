/**
 * Canvas behaviour for the Filament Page Builder.
 *
 * Hand-written and shipped unminified. Uses the browser's native drag-and-drop rather
 * than a library: Filament bundles SortableJS but does not expose it as a public global,
 * and the package must not require consumers to run a JS build — the whole point is that
 * it works on hosts with no Node installed.
 *
 * Drop targets are slots (columns) and the root canvas. The pointer resolves to the
 * nearest valid well, so a block can land beside its siblings or inside a section.
 * Reordering is committed to Livewire in a single call per drop. Where the block will
 * land is drawn on an overlay beside the canvas rather than inserted into it, so showing
 * the spot can never move the page it is pointing at.
 *
 * Also here: the right-click menu, copy and paste (through the system clipboard and a
 * local buffer), dragging a block's edges or a section's column edges to resize, and the
 * Preview frame.
 *
 * Motion is Anime.js, vendored beside this file. It is deliberately optional: every
 * animation goes through `motion`, which no-ops when the library is absent or when the
 * reader has asked for reduced motion, and the editor stays fully usable either way.
 */

/**
 * The editor's motion vocabulary.
 *
 * Nothing here changes what the canvas *does* — every one of these runs after the state
 * has already changed, purely to show which thing changed and where it went. A page
 * builder edits by replacing chunks of the document out from under you, and without
 * some continuity a drop reads as "nothing happened" even when it worked.
 */
const motion = {
    /** Durations in ms, kept together so the whole editor stays on one rhythm. */
    duration: { flash: 620, enter: 420, exit: 200, move: 380 },

    get anime() {
        return typeof window.anime === 'function' ? window.anime : null;
    },

    /**
     * Whether to animate at all.
     *
     * A hidden tab has no business running timelines, and `prefers-reduced-motion` is a
     * request, not a hint — in a tool people use all day it is the difference between
     * usable and nauseating.
     */
    get enabled() {
        return (
            this.anime !== null &&
            document.visibilityState === 'visible' &&
            !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        );
    },

    /**
     * Run `params` through Anime.js.
     *
     * When motion is off the animation is skipped but its `complete` callback still
     * fires, straight away — callers hang real work off it (removing a block, tidying
     * up inline styles) and that work has to happen either way.
     */
    play(params) {
        if (!this.enabled) {
            params.complete?.();

            return null;
        }

        return this.anime(params);
    },

    /**
     * A ring that blooms out of a block and fades.
     *
     * Drawn on a throwaway overlay rather than the block's own outline so it can sit on
     * top of the block's content without the block's styles having to know about it, and
     * so two flashes in a row cannot fight over one property.
     */
    flash(el, tone = 'accent') {
        if (!el || !this.enabled) {
            return;
        }

        const ring = document.createElement('div');
        ring.className = 'fpb-flash';
        ring.dataset.tone = tone;
        el.appendChild(ring);

        this.anime({
            targets: ring,
            opacity: [0, 1, 0],
            scale: [0.985, 1.012],
            easing: 'easeOutQuad',
            duration: this.duration.flash,
            complete: () => ring.remove(),
        });
    },

    /** Clear anything a timeline left behind on an element it no longer owns. */
    reset(el) {
        if (el) {
            el.style.opacity = '';
            el.style.transform = '';
        }
    },
};

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pageBuilderCanvas', () => ({
        /*
         * Everything here looks elements up from `$root`, never `$el`.
         *
         * These methods are called from `x-on` expressions all over the tree, and inside
         * such an expression `$el` is the element the listener sits on — the canvas, a
         * palette button, a drag handle — not the component root. `canvas()` resolving
         * against the canvas itself returned null, which made every drag silently do
         * nothing. `$root` is the same element whichever handler we came in through.
         */

        /** 'move' while dragging an existing block, 'insert' from the palette, null otherwise. */
        mode: null,
        payload: null,
        target: null,
        teardown: [],
        sideTab: 'blocks',
        inspectorTab: 'content',
        preview: 'desktop',
        paletteQuery: '',
        /** The inline field the caret is in, named for the breadcrumb. */
        editingField: null,
        /** The inspector at double width, for code and long forms. */
        wideInspector: false,

        /** The right-click menu: where it is and what it was opened on. */
        menu: { open: false, x: 0, y: 0, id: null, label: '', parent: false, container: false },
        /** A copied block (clipboard JSON) and a copied look, kept in this browser too. */
        clipboard: null,
        styleClipboard: null,

        previewing: false,
        previewLoading: false,
        previewDevice: 'desktop',

        /** A drag of a block edge or a column edge in progress. */
        resizing: null,
        /** A short confirmation shown beside the save status ("Copied"). */
        notice: null,
        noticeTimer: null,
        indicatorKey: null,
        overlayQueued: false,
        sizeObserver: null,
        isMac: typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform),
        /**
         * Below 1280px the three columns cannot sit together. One workspace at a
         * time, switched from the dock: blocks · canvas · settings.
         */
        workspace: 'canvas',
        narrow: typeof window.matchMedia === 'function' && window.matchMedia('(max-width: 1280px)').matches,

        /* ── Motion bookkeeping (not reactive state the template reads) ─── */

        /**
         * Translations for this locale, read from `data-fpb-i18n` in init().
         *
         * Deliberately not reactive: nothing re-renders it, and a locale change is a full
         * page load. Reactivity here would only cost a diff on every notice.
         */
        strings: {},

        /** Block ids already on the canvas, so a re-render can tell new from moved. */
        seen: null,
        /** id → bounding box, taken just before a change, for the FLIP afterwards. */
        before: null,
        observer: null,
        /** rAF handle and pixels-per-frame for the edge autoscroll while dragging. */
        scrolling: null,
        scrollSpeed: 0,
        /** When `before` was taken, so a click that changed nothing cannot mislead us. */
        capturedAt: 0,
        /** A block that was just moved, to be pointed out once it has finished sliding. */
        landing: null,

        init() {
            // Translations arrive on the root element from `DesignPage::canvasStrings()`.
            // There is no bundler and no import map here, so this attribute is the whole
            // i18n layer — a key read straight out of the source instead of through `t()`
            // would stay English in every locale but English.
            this.strings = this.readStrings();

            this.bind(window, 'beforeunload', (event) => this.guardUnload(event));
            this.bind(document, 'keydown', (event) => this.onKeydown(event));

            // Inline editing. Delegated from the root so blocks re-rendered by Livewire
            // are covered without rebinding anything.
            this.bind(this.$root, 'focusin', (event) => this.onEditableFocus(event));
            this.bind(this.$root, 'focusout', (event) => this.onEditableBlur(event));
            this.bind(this.$root, 'keydown', (event) => this.onEditableKeydown(event), true);

            // Filament navigates between panel pages without a page load, so the browser's
            // own unload prompt never fires. Livewire's navigate event is the only chance
            // to stop the canvas being left with unsaved work.
            this.bind(document, 'livewire:navigate', (event) => this.guardNavigate(event));

            // Positions are taken before the click that changes them, so anything the
            // toolbar or a block's own buttons set off — undo, redo, duplicate, delete —
            // can be animated without every one of them having to say so.
            this.bind(this.$root, 'pointerdown', () => this.captureRects(), true);

            // Copy and paste of whole blocks. Only when nothing is being typed: inside a
            // field these are the browser's own, and must stay that way.
            this.bind(document, 'paste', (event) => this.onPaste(event));

            this.bind(window, 'resize', () => {
                this.closeMenu();
                this.refreshOverlay();
            });
            this.bind(this.frame() ?? window, 'scroll', () => this.closeMenu(), true);

            this.restoreClipboard();
            this.watchNarrow();

            this.seen = new Set(this.order());
            this.watchCanvas();
            this.watchSize();

            this.$watch('preview', () => setTimeout(() => this.refreshOverlay(), 230));
            this.$watch('wideInspector', () => setTimeout(() => this.refreshOverlay(), 60));
        },

        /**
         * Parse the translations the server put on the root element.
         *
         * Falls back to an empty object rather than throwing: a malformed payload should
         * cost one untranslated label, not take the whole canvas down with it. `t()` then
         * shows the key, which is obvious enough to notice in a screenshot.
         */
        readStrings() {
            const raw = this.$root?.dataset?.fpbI18n;

            if (!raw) {
                return {};
            }

            try {
                const parsed = JSON.parse(raw);

                return parsed && typeof parsed === 'object' ? parsed : {};
            } catch {
                return {};
            }
        },

        /**
         * A translated string from the `js` group, with `:name` replaced.
         *
         * The server has already filled any gap in the locale with English, so a key
         * missing here is missing from English too: the source asks for a string nobody
         * wrote. That shows as the key, page-builder.js.confirm_delete, which is a bug
         * report rather than a blank.
         */
        t(key, replace = {}) {
            const value = this.strings?.[key] ?? `page-builder.js.${key}`;

            return Object.entries(replace).reduce(
                (text, [name, replacement]) => text.replaceAll(`:${name}`, String(replacement)),
                value,
            );
        },

        /**
         * Alpine's teardown hook. The listeners above live on `window` and `document`,
         * so nothing else would ever take them off: Filament's SPA navigation swaps the
         * page out without a reload, and a second visit would otherwise stack a fresh
         * set on top of the last.
         */
        destroy() {
            this.teardown.forEach((off) => off());
            this.teardown = [];
            this.observer?.disconnect();
            this.observer = null;
            this.sizeObserver?.disconnect();
            this.sizeObserver = null;
            this.stopAutoscroll();
            this.stopResize();
        },

        /**
         * Treat a laptop split-screen or a phone as a one-panel editor.
         *
         * The desktop grid needs ~16 + 20 rem of side chrome. Stacking all three
         * columns and scrolling the page used to bury the canvas. The dock keeps
         * the editor at 100dvh and shows one surface.
         */
        watchNarrow() {
            if (typeof window.matchMedia !== 'function') {
                return;
            }

            const query = window.matchMedia('(max-width: 1280px)');
            const sync = () => {
                this.narrow = query.matches;
            };

            sync();

            if (typeof query.addEventListener === 'function') {
                query.addEventListener('change', sync);
                this.teardown.push(() => query.removeEventListener('change', sync));

                return;
            }

            query.addListener(sync);
            this.teardown.push(() => query.removeListener(sync));
        },

        showWorkspace(name) {
            this.workspace = name;

            if (name === 'blocks') {
                this.sideTab = 'blocks';
            }

            if (name === 'settings') {
                this.inspectorTab = 'content';
            }
        },

        /**
         * After a palette tap on a narrow viewport, go back to the page so the
         * new block is visible. Desktop keeps all three columns.
         */
        afterPaletteInsert() {
            if (this.narrow) {
                this.workspace = 'canvas';
            }
        },

        /**
         * Outline and ghost actions select a block that lives on the canvas.
         */
        afterRevealOnCanvas() {
            if (this.narrow) {
                this.workspace = 'canvas';
            }
        },

        bind(target, event, handler, capture = false) {
            target.addEventListener(event, handler, capture);
            this.teardown.push(() => target.removeEventListener(event, handler, capture));
        },

        /* ── Showing what changed ───────────────────────── */

        /**
         * Watch the canvas and narrate whatever Livewire just did to it.
         *
         * Livewire replaces markup without saying what changed, so the canvas works it
         * out by diffing: an id that was not there before has arrived, an id whose box
         * has moved was reordered. Reading the DOM rather than hooking each action means
         * undo, redo, duplicate and the inspector are all covered by the same code.
         */
        watchCanvas() {
            const canvas = this.canvas();

            if (!canvas || typeof MutationObserver === 'undefined') {
                return;
            }

            let queued = false;

            this.observer = new MutationObserver((records) => {
                if (queued || records.every((record) => this.isOwnChrome(record))) {
                    return;
                }

                queued = true;

                // A single morph fires dozens of records; one pass per frame is plenty.
                requestAnimationFrame(() => {
                    queued = false;
                    this.settle();
                });
            });

            this.observer.observe(canvas, { childList: true, subtree: true });
        },

        /**
         * Whether a mutation is just the editor's own decoration.
         *
         * The flash ring comes and goes on its own inside a block, and would otherwise
         * read as the page having changed. (The drop indicator and the column handles live
         * on the overlay, outside the canvas, so they never reach the observer at all.)
         */
        isOwnChrome(record) {
            const nodes = [...record.addedNodes, ...record.removedNodes];

            return (
                nodes.length > 0 &&
                nodes.every((node) => node instanceof HTMLElement && node.classList.contains('fpb-flash'))
            );
        },

        /** Where every block sits right now, to compare against once it has changed. */
        captureRects() {
            if (!motion.enabled) {
                return;
            }

            this.capturedAt = Date.now();
            this.before = new Map(
                Array.from(this.$root.querySelectorAll('.fpb-block')).map((el) => [
                    el.dataset.id,
                    el.getBoundingClientRect(),
                ])
            );
        },

        /** The canvas has settled: greet what is new, carry what moved. */
        settle() {
            const blocks = Array.from(this.$root.querySelectorAll('.fpb-block'));

            // A capture belongs to the change it preceded. If a second has gone by, the
            // click it came from did nothing to the canvas and the boxes are stale.
            const before = Date.now() - this.capturedAt < 1500 ? this.before : null;

            this.before = null;

            blocks.forEach((el) => {
                if (!this.seen.has(el.dataset.id)) {
                    return this.enter(el);
                }

                const was = before?.get(el.dataset.id);

                if (was) {
                    this.slide(el, was);
                }
            });

            // A block that merely moved gets no entrance, so without this the reader has
            // to work out for themselves which of seven sliding blocks was theirs.
            if (this.landing) {
                motion.flash(this.blockEl(this.landing));
                this.landing = null;
            }

            this.seen = new Set(blocks.map((el) => el.dataset.id));
            this.refreshOverlay();
        },

        /** A block that was not on the page a moment ago. */
        enter(el) {
            if (motion.enabled) {
                // Set before the first frame Anime.js gets, so the block never shows at
                // full strength and then starts its entrance from nothing.
                el.style.opacity = '0';
            }

            motion.play({
                targets: el,
                opacity: [0, 1],
                translateY: [14, 0],
                scale: [0.985, 1],
                easing: 'easeOutCubic',
                duration: motion.duration.enter,
                complete: () => motion.reset(el),
            });

            motion.flash(el);
            this.revealBlock(el);
        },

        /**
         * FLIP. The block is already where it belongs, so put it back visually and let
         * it travel: transforms cost nothing per frame, whereas animating the layout
         * itself would reflow the whole page sixty times a second.
         */
        slide(el, was) {
            const now = el.getBoundingClientRect();
            const dx = was.left - now.left;
            const dy = was.top - now.top;

            // Sub-pixel drift from a scrollbar appearing is not a move worth showing.
            if (Math.abs(dx) < 2 && Math.abs(dy) < 2) {
                return;
            }

            motion.play({
                targets: el,
                translateX: [dx, 0],
                translateY: [dy, 0],
                easing: 'easeOutQuad',
                duration: motion.duration.move,
                complete: () => motion.reset(el),
            });
        },

        /**
         * Bring a block into view inside the canvas.
         *
         * The canvas is its own scroller, so a block inserted below the fold would land
         * out of sight and read as nothing having happened at all.
         */
        revealBlock(el) {
            const frame = this.$root.querySelector('.fpb-canvas-frame');

            if (!frame || !el) {
                return;
            }

            const box = el.getBoundingClientRect();
            const view = frame.getBoundingClientRect();

            if (box.top >= view.top && box.bottom <= view.bottom) {
                return;
            }

            const centred = (view.height - Math.min(box.height, view.height)) / 2;
            const to = Math.max(0, frame.scrollTop + (box.top - view.top) - centred);

            if (!motion.enabled) {
                frame.scrollTop = to;

                return;
            }

            // Animate a number and write it across, rather than asking Anime.js to guess
            // what kind of property `scrollTop` is.
            const at = { top: frame.scrollTop };

            motion.play({
                targets: at,
                top: to,
                easing: 'easeInOutQuad',
                duration: 420,
                update: () => {
                    frame.scrollTop = at.top;
                },
            });
        },

        /**
         * Scroll the canvas when a drag reaches its edge.
         *
         * Native drag-and-drop will not scroll a nested scroller for you, so without
         * this there is no way to drop a block anywhere that is not already on screen —
         * which, on a real page, is most of it.
         */
        autoscroll(event) {
            const frame = this.$root.querySelector('.fpb-canvas-frame');

            if (!frame) {
                return;
            }

            const view = frame.getBoundingClientRect();
            const zone = Math.min(110, view.height / 4);
            const fromTop = event.clientY - view.top;
            const fromBottom = view.bottom - event.clientY;

            if (fromTop < zone) {
                this.scrollSpeed = -Math.ceil(((zone - fromTop) / zone) * 20);
            } else if (fromBottom < zone) {
                this.scrollSpeed = Math.ceil(((zone - fromBottom) / zone) * 20);
            } else {
                return this.stopAutoscroll();
            }

            if (this.scrolling !== null) {
                return;
            }

            const step = () => {
                frame.scrollTop += this.scrollSpeed;
                this.scrolling = requestAnimationFrame(step);
            };

            this.scrolling = requestAnimationFrame(step);
        },

        stopAutoscroll() {
            if (this.scrolling !== null) {
                cancelAnimationFrame(this.scrolling);
                this.scrolling = null;
            }

            this.scrollSpeed = 0;
        },

        /* ── Leaving with unsaved work ──────────────────── */

        guardUnload(event) {
            if (!this.$wire.isDirty) {
                return;
            }

            event.preventDefault();
            // Browsers ignore the message and show their own, but older ones need a value.
            event.returnValue = '';
        },

        guardNavigate(event) {
            if (!this.$wire.isDirty) {
                return;
            }

            if (!window.confirm(this.t('confirm_unsaved'))) {
                event.preventDefault();
            }
        },

        /* ── Keyboard ───────────────────────────────────── */

        /**
         * Whether the user is typing, in which case the canvas keeps its hands off.
         *
         * Covers the inspector's inputs and, from Stage B onwards, text being edited
         * directly on the page.
         */
        isTyping(event) {
            const el = event.target;

            return (
                el instanceof HTMLElement &&
                (el.isContentEditable ||
                    ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName) ||
                    el.closest('[contenteditable]') !== null)
            );
        },

        /**
         * Commit the field under the caret before a save or navigation.
         *
         * Commit is blur-driven. ⌘S used to fire while the caret was still in the
         * field, so the shortcut saved the previous value and then the later blur
         * made the page dirty again.
         */
        flushActiveEditable() {
            const active = document.activeElement;

            if (active instanceof HTMLElement && this.$root.contains(active) && this.editableFrom({ target: active })) {
                active.blur();
            }
        },

        matchesPalette(haystack) {
            const query = this.paletteQuery.trim().toLowerCase();

            return query === '' || String(haystack).toLowerCase().includes(query);
        },

        onKeydown(event) {
            const chord = event.metaKey || event.ctrlKey;

            if (event.key === 'Escape' && this.previewing) {
                event.preventDefault();

                return this.closePreview();
            }

            if (event.key === 'Escape' && this.menu.open) {
                event.preventDefault();

                return this.closeMenu();
            }

            if (chord && event.key.toLowerCase() === 's') {
                event.preventDefault();
                this.flushActiveEditable();

                return this.$wire.save();
            }

            if (this.previewing || this.isTyping(event)) {
                return;
            }

            if (chord && event.key.toLowerCase() === 'c' && this.$wire.selectedId && !this.hasTextSelection()) {
                event.preventDefault();

                return this.copyBlock(this.$wire.selectedId);
            }

            if (chord && event.key.toLowerCase() === 'z') {
                event.preventDefault();

                return event.shiftKey ? this.$wire.redo() : this.$wire.undo();
            }

            const selected = this.$wire.selectedId;

            if (event.key === 'Escape') {
                return this.$wire.selectBlock(null);
            }

            if (!selected) {
                return;
            }

            if (chord && event.key.toLowerCase() === 'd') {
                event.preventDefault();

                return this.$wire.duplicateBlock(selected);
            }

            if (event.key === 'Backspace' || event.key === 'Delete') {
                event.preventDefault();

                return this.remove(selected, this.selectedHasContent());
            }

            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                event.preventDefault();

                const step = event.key === 'ArrowDown' ? 1 : -1;

                return event.shiftKey
                    ? this.moveSelected(selected, step)
                    : this.selectNeighbour(selected, step);
            }
        },

        /** Block ids in the order they appear on the canvas, nested children included. */
        order() {
            return Array.from(this.$root.querySelectorAll('.fpb-block')).map((el) => el.dataset.id);
        },

        selectedHasContent() {
            const el = this.$root.querySelector('.fpb-block[data-selected="true"]');

            return el ? el.dataset.hasContent === 'true' : false;
        },

        selectNeighbour(id, step) {
            const order = this.order();
            const next = order[order.indexOf(id) + step];

            if (next) {
                this.$wire.selectBlock(next);
                this.revealBlock(this.blockEl(next));
            }
        },

        moveSelected(id, step) {
            const el = this.blockEl(id);

            if (!el) {
                return;
            }

            const siblings = this.directBlocks(this.slotOf(el) ?? this.canvas());
            const from = siblings.indexOf(el);
            const to = from + step;

            if (from < 0 || to < 0 || to >= siblings.length) {
                return;
            }

            const parent = el.dataset.parent || null;
            const slot = el.dataset.slot || null;

            this.captureRects();
            this.landing = id;

            // moveBlock takes the index in the sibling list before the block is lifted
            // out, so moving down by one has to aim one past the neighbour it swaps with.
            this.$wire.moveBlock(id, step > 0 ? to + 1 : to, parent, slot);
        },

        /* ── Editing text on the page ───────────────────── */

        /** The editable field an event happened inside, if any. */
        editableFrom(event) {
            const el = event.target;

            return el instanceof HTMLElement ? el.closest('[data-fpb-field]') : null;
        },

        onEditableFocus(event) {
            const el = this.editableFrom(event);

            if (!el) {
                return;
            }

            // Remember what was there, so Escape has something to go back to.
            el.dataset.fpbOriginal = el.innerText;
            this.editingField = this.humanise(el.dataset.fpbField);

            // Typing in a block is a way of choosing it. The inspector is a second view
            // of the same fields and should follow the caret — and show which of them
            // this element is, so clicking a button inside a block says "you are editing
            // the button" rather than leaving the editor to hunt for it.
            if (this.$wire.selectedId !== el.dataset.fpbBlock) {
                this.$wire.selectBlock(el.dataset.fpbBlock).then(() => this.revealField(el.dataset.fpbField));
            } else {
                this.revealField(el.dataset.fpbField);
            }
        },

        /**
         * Commit on blur rather than on every keystroke.
         *
         * A round trip per character would have Livewire re-render the block under the
         * caret. Committing once, when the caret has already left, is both cheaper and
         * the same thing the inspector's own fields do.
         */
        onEditableBlur(event) {
            const el = this.editableFrom(event);

            if (!el) {
                return;
            }

            const value = el.innerText;
            const original = el.dataset.fpbOriginal;

            delete el.dataset.fpbOriginal;
            this.editingField = null;

            if (value === original) {
                return;
            }

            this.$wire.setBlockField(el.dataset.fpbBlock, el.dataset.fpbField, value);
        },

        onEditableKeydown(event) {
            const el = this.editableFrom(event);

            if (!el) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();

                el.innerText = el.dataset.fpbOriginal ?? '';
                delete el.dataset.fpbOriginal;

                return el.blur();
            }

            if (event.key === 'Enter' && el.dataset.fpbMultiline !== 'true') {
                event.preventDefault();

                return el.blur();
            }
        },

        /**
         * Delete, but let the block leave first.
         *
         * The server rewrites the page around it either way; playing the exit before the
         * call makes the gap closing up read as one movement instead of a block blinking
         * out and everything below it jumping.
         */
        remove(id, hasContent) {
            if (hasContent && !window.confirm(this.t('confirm_delete'))) {
                return;
            }

            const el = this.blockEl(id);

            this.captureRects();

            motion.play({
                targets: el,
                opacity: [1, 0],
                translateX: [0, -18],
                scale: [1, 0.97],
                easing: 'easeInQuad',
                duration: motion.duration.exit,
                complete: () => {
                    motion.reset(el);
                    this.$wire.removeBlock(id);
                },
            });
        },

        startMove(event, id) {
            this.closeMenu();
            this.clearColumnHandles();
            this.mode = 'move';
            this.payload = id;
            event.dataTransfer.effectAllowed = 'move';
            // Firefox refuses to start a drag unless something is set.
            event.dataTransfer.setData('text/plain', id);

            const el = this.blockEl(id);

            if (el) {
                el.dataset.dragging = 'true';
            }

            this.lift(event.currentTarget);
        },

        startInsert(event, type) {
            this.closeMenu();
            this.clearColumnHandles();
            this.mode = 'insert';
            this.payload = type;
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', type);

            this.lift(event.currentTarget);
        },

        /**
         * A press-down on the thing you just picked up.
         *
         * The browser has already photographed the drag image by the time this runs, so
         * this only ever moves what stays behind — which is the point: it acknowledges
         * the grab without the ghost under the cursor twitching.
         */
        lift(el) {
            motion.play({
                targets: el,
                scale: [1, 0.95, 1],
                easing: 'easeOutQuad',
                duration: 280,
                complete: () => motion.reset(el),
            });
        },

        clearDrag() {
            this.mode = null;
            this.payload = null;
            this.target = null;
            this.hideIndicator();
            this.clearSlotHighlight();
            this.stopAutoscroll();

            this.$root.querySelectorAll('.fpb-block[data-dragging]').forEach((el) => {
                delete el.dataset.dragging;
            });

            this.refreshOverlay();
        },

        canvas() {
            return this.$root.querySelector('.fpb-canvas');
        },

        blockEl(id) {
            return this.$root.querySelector(`.fpb-block[data-id="${this.cssEscape(id)}"]`);
        },

        cssEscape(value) {
            return typeof CSS !== 'undefined' && CSS.escape ? CSS.escape(value) : value.replace(/"/g, '\\"');
        },

        slotOf(el) {
            return el.closest('.fpb-slot');
        },

        /** Direct child blocks of a slot or the root canvas — not nested descendants. */
        directBlocks(container) {
            if (!container) {
                return [];
            }

            return Array.from(container.children).filter((el) => el.classList?.contains('fpb-block'));
        },

        /**
         * Whether dropping `id` into `parent` would nest a block inside itself.
         */
        isInvalidParent(id, parent) {
            if (!id || !parent) {
                return false;
            }

            if (id === parent) {
                return true;
            }

            const parentEl = this.blockEl(parent);

            return parentEl !== null && this.blockEl(id)?.contains(parentEl);
        },

        /**
         * The slot or root canvas the pointer is over, and the sibling index it would
         * land at. Prefers an inner column when the pointer is inside one, so dropping
         * onto a section does not always bounce the block back to the page root.
         */
        targetFromEvent(event) {
            const canvas = this.canvas();

            if (!canvas) {
                return null;
            }

            const slot = event.target instanceof Element ? event.target.closest('.fpb-slot') : null;
            const overCanvas = event.target instanceof Element && canvas.contains(event.target);

            if (!overCanvas && event.target !== canvas) {
                // Still allow the empty-canvas drop, when the event target is the canvas itself.
            }

            let container = canvas;
            let parent = null;
            let slotName = null;

            if (slot && canvas.contains(slot)) {
                container = slot;
                parent = slot.dataset.fpbParent || null;
                slotName = slot.dataset.fpbSlot || null;
            }

            if (this.mode === 'move' && this.isInvalidParent(this.payload, parent)) {
                return null;
            }

            const blocks = this.directBlocks(container);
            let index = blocks.length;

            for (let i = 0; i < blocks.length; i++) {
                if (this.mode === 'move' && blocks[i].dataset.id === this.payload) {
                    continue;
                }

                const box = blocks[i].getBoundingClientRect();

                if (event.clientY < box.top + box.height / 2) {
                    index = i;

                    break;
                }
            }

            // The sibling index we send to Livewire includes the dragged block when it
            // is already in this group, matching moveBlock's "before lift" contract.
            if (this.mode === 'move') {
                const dragged = blocks.find((el) => el.dataset.id === this.payload);

                if (dragged) {
                    const draggedIndex = blocks.indexOf(dragged);

                    if (draggedIndex !== -1 && draggedIndex < index) {
                        // Walking past the dragged block: the visual gap after it is
                        // already "its current index + 1" in the before-lift list.
                    }
                } else {
                    // Coming from another slot: `index` is the destination sibling list
                    // as it stands, which is what insert/move expect for a new group.
                }
            }

            return { parent, slot: slotName, index, container };
        },

        onDragOver(event) {
            if (!this.mode) {
                return;
            }

            event.dataTransfer.dropEffect = this.mode === 'move' ? 'move' : 'copy';

            const next = this.targetFromEvent(event);

            this.target = next;
            this.highlightSlot(next?.container);
            this.showIndicator(next);
            this.autoscroll(event);
        },

        onDragLeave(event) {
            if (!this.$root.contains(event.relatedTarget)) {
                this.hideIndicator();
                this.clearSlotHighlight();
            }
        },

        onDrop(event) {
            if (!this.mode) {
                return;
            }

            const next = this.targetFromEvent(event) ?? this.target;
            const mode = this.mode;
            const payload = this.payload;

            this.clearDrag();

            if (!next) {
                return;
            }

            // Taken here rather than at `dragstart`: a drag lasts as long as the reader
            // takes to aim, and these boxes are only good for about a second.
            this.captureRects();

            if (mode === 'move') {
                this.landing = payload;
                this.$wire.moveBlock(payload, next.index, next.parent, next.slot);
            } else {
                this.$wire.insertBlock(payload, next.index, next.parent, next.slot).then((id) => this.focusEditable(id));
            }
        },

        highlightSlot(container) {
            if (container && container.dataset.dropActive === 'true') {
                return;
            }

            this.clearSlotHighlight();

            if (container && container.classList.contains('fpb-slot')) {
                container.dataset.dropActive = 'true';
            }
        },

        clearSlotHighlight() {
            this.$root.querySelectorAll('.fpb-slot[data-drop-active]').forEach((el) => {
                delete el.dataset.dropActive;
            });
        },

        /* ── Where a drop will land ─────────────────────── */

        frame() {
            return this.$root.querySelector('.fpb-canvas-frame');
        },

        /**
         * An element's box in overlay coordinates.
         *
         * The overlay lives inside the scrolling frame, so its origin is the frame's
         * content, not the viewport: a box measured this way scrolls with the page and
         * never needs re-measuring because the reader scrolled.
         */
        overlayBox(el) {
            const frame = this.frame();
            const outer = frame.getBoundingClientRect();
            const box = el.getBoundingClientRect();
            const left = box.left - outer.left + frame.scrollLeft;
            const top = box.top - outer.top + frame.scrollTop;

            return { left, top, width: box.width, height: box.height, right: left + box.width, bottom: top + box.height };
        },

        /**
         * Draw "it goes here" for a drop target.
         *
         * The old marker was a 3px element inserted into the page between two blocks.
         * Inserting it pushed everything below down by 3px, which moved the midpoints the
         * pointer is measured against, which moved the marker back — so it flickered
         * between two places on every pointer move, and each move restarted its entrance
         * animation. The indicator is now drawn on an overlay that takes no space, is only
         * touched when the target really changes, and glides between positions.
         */
        showIndicator(target) {
            const indicator = this.$refs.indicator;

            if (!indicator || !target || !target.container || !this.frame()) {
                return this.hideIndicator();
            }

            const container = target.container;
            const all = this.directBlocks(container);
            const others = all.filter((el) => !(this.mode === 'move' && el.dataset.id === this.payload));
            const well = this.overlayBox(container);
            const inset = container.classList.contains('fpb-slot') ? 6 : 14;
            let kind = 'line';
            let top = well.top;
            let height = 4;

            if (others.length === 0) {
                // An empty column: light up the whole well rather than a line inside it.
                kind = 'zone';
                top = well.top + 4;
                height = Math.max(28, well.height - 8);
            } else if (target.index < all.length) {
                const next = this.overlayBox(all[target.index]);
                const previous = target.index > 0 ? this.overlayBox(all[target.index - 1]) : null;

                top = previous ? (previous.bottom + next.top) / 2 : next.top;
            } else {
                top = this.overlayBox(all[all.length - 1]).bottom;
            }

            const left = well.left + inset;
            const width = Math.max(24, well.width - inset * 2);
            const label = `${this.mode === 'move' ? this.t('move_here') : this.t('add_here')} · ${this.placeName(container)}`;
            const key = [kind, Math.round(left), Math.round(top), Math.round(width), Math.round(height), label].join('|');

            if (key === this.indicatorKey) {
                return;
            }

            const first = indicator.dataset.visible !== 'true';

            this.indicatorKey = key;

            if (first) {
                // Appear where it belongs, instead of gliding in from the last target.
                indicator.style.transition = 'none';
            }

            indicator.dataset.kind = kind;
            indicator.style.transform = `translate3d(${left}px, ${kind === 'line' ? top - 2 : top}px, 0)`;
            indicator.style.width = `${width}px`;
            indicator.style.height = `${height}px`;

            if (this.$refs.indicatorLabel) {
                this.$refs.indicatorLabel.textContent = label;
            }

            if (first) {
                void indicator.offsetWidth;
                indicator.style.transition = '';
                indicator.dataset.visible = 'true';
            }
        },

        hideIndicator() {
            const indicator = this.$refs.indicator;

            this.indicatorKey = null;

            if (indicator) {
                delete indicator.dataset.visible;
            }
        },

        /** "page", or "column 2" — what the indicator says it is dropping into. */
        placeName(container) {
            if (!container.classList.contains('fpb-slot')) {
                return this.t('place_page');
            }

            const match = /(\d+)$/.exec(container.dataset.fpbSlot ?? '');

            return match ? this.t('place_column_n', { number: Number(match[1]) + 1 }) : this.t('place_column');
        },

        /* ── Editor decoration: column handles and readouts ── */

        watchSize() {
            const canvas = this.canvas();

            if (!canvas || typeof ResizeObserver === 'undefined') {
                return;
            }

            this.sizeObserver = new ResizeObserver(() => this.refreshOverlay());
            this.sizeObserver.observe(canvas);
        },

        refreshOverlay() {
            if (this.overlayQueued) {
                return;
            }

            this.overlayQueued = true;

            requestAnimationFrame(() => {
                this.overlayQueued = false;

                if (!this.resizing) {
                    this.mountColumnHandles();
                }
            });
        },

        clearColumnHandles() {
            this.$refs.overlay?.querySelectorAll('.fpb-col-handle').forEach((el) => el.remove());
        },

        /**
         * Grab handles on the edges between a selected section's columns.
         *
         * Drawn on the overlay, not in the section, so they survive nothing and cost
         * nothing: they are rebuilt from the section's live geometry whenever the canvas
         * settles, and a drag previews by restyling the grid in place before one call
         * commits it.
         */
        mountColumnHandles() {
            this.clearColumnHandles();

            const overlay = this.$refs.overlay;

            if (!overlay || this.narrow || this.mode || this.previewing || !this.frame()) {
                return;
            }

            const block = this.$root.querySelector('.fpb-block[data-selected="true"][data-container="true"]');
            const section = block?.querySelector(':scope > .fpb-block-body > [data-fpb-tracks]');

            if (!section) {
                return;
            }

            const slots = this.slotsOf(section);

            if (slots.length < 2) {
                return;
            }

            const boxes = slots.map((el) => this.overlayBox(el));

            // Stacked on a narrow canvas: there is no edge between them to drag.
            if (boxes[1].top >= boxes[0].bottom - 1) {
                return;
            }

            for (let i = 0; i < slots.length - 1; i++) {
                const handle = document.createElement('div');

                handle.className = 'fpb-col-handle';
                handle.title = this.t('drag_resize_columns');
                this.placeColumnHandle(handle, boxes[i], boxes[i + 1]);
                handle.addEventListener('pointerdown', (event) => this.startColumnResize(event, block.dataset.id, section, i));
                overlay.appendChild(handle);
            }
        },

        placeColumnHandle(handle, left, right) {
            const x = (left.right + right.left) / 2;
            const top = Math.min(left.top, right.top);
            const bottom = Math.max(left.bottom, right.bottom);

            handle.style.transform = `translate3d(${x}px, ${top}px, 0)`;
            handle.style.height = `${bottom - top}px`;
        },

        slotsOf(section) {
            return Array.from(section.children).filter((el) => el.classList.contains('fpb-slot'));
        },

        showTip(text, event) {
            const tip = this.$refs.sizeTip;
            const frame = this.frame();

            if (!tip || !frame) {
                return;
            }

            const outer = frame.getBoundingClientRect();

            tip.textContent = text;
            tip.style.transform = `translate3d(${event.clientX - outer.left + frame.scrollLeft + 14}px, ${event.clientY - outer.top + frame.scrollTop + 14}px, 0)`;
            tip.dataset.visible = 'true';
        },

        hideTip() {
            if (this.$refs.sizeTip) {
                delete this.$refs.sizeTip.dataset.visible;
            }
        },

        /* ── Resizing ─────────────────────────────────────── */

        /**
         * Drag a selected block's right edge (width) or bottom edge (height).
         *
         * The block is restyled locally while the pointer moves and the value is sent
         * once, on release, so the whole drag is one undo step. Only inline styles change
         * during the drag — never the DOM tree — so the canvas's mutation observer does
         * not mistake a resize for blocks being reordered.
         */
        startResize(event, id, axis) {
            const block = this.blockEl(id);
            const body = block?.querySelector(':scope > .fpb-block-body');

            if (!block || !body || event.button > 0) {
                return;
            }

            const spacer = axis === 'height' && block.dataset.type === 'spacer' ? body.querySelector('.fpb-spacer') : null;
            const parent = block.parentElement;

            this.closeMenu();
            this.resizing = {
                kind: 'block',
                id,
                axis,
                block,
                body,
                spacer,
                startX: event.clientX,
                startY: event.clientY,
                startWidth: block.getBoundingClientRect().width,
                startHeight: (spacer ?? body).getBoundingClientRect().height,
                parentWidth: parent?.clientWidth || parent?.getBoundingClientRect().width || 1,
                value: null,
            };

            this.listenForResize(axis === 'width' ? 'x' : 'y');
        },

        moveResize(event) {
            const resize = this.resizing;

            if (!resize) {
                return;
            }

            if (resize.kind === 'columns') {
                return this.moveColumnResize(event);
            }

            if (resize.axis === 'width') {
                const width = resize.startWidth + (event.clientX - resize.startX);
                const percent = Math.max(5, Math.min(100, Math.round((width / resize.parentWidth) * 100)));

                resize.value = percent;
                resize.block.style.width = percent === 100 ? '' : `${percent}%`;

                return this.showTip(
                    percent === 100 ? this.t('full_width') : this.t('percent_wide', { percent }),
                    event,
                );
            }

            const height = Math.max(resize.spacer ? 4 : 0, Math.min(4000, Math.round((resize.startHeight + (event.clientY - resize.startY)) / 4) * 4));

            resize.value = height;

            if (resize.spacer) {
                resize.spacer.style.height = `${height}px`;
            } else {
                resize.body.style.minHeight = `${height}px`;
            }

            this.showTip(this.t('pixels_tall', { height }), event);
        },

        endResize() {
            const resize = this.resizing;

            this.stopResize();

            if (!resize || resize.value === null) {
                return;
            }

            // The block already sits at its new size; a FLIP from before the drag would
            // replay the whole resize as a slide once the server answers.
            this.before = null;
            this.capturedAt = 0;

            if (resize.kind === 'columns') {
                if (resize.tracks.join('-') !== resize.start.join('-')) {
                    this.$wire.resizeColumns(resize.id, resize.tracks);
                }

                return;
            }

            const value = resize.axis === 'width' && resize.value >= 100 ? null : resize.value;

            this.$wire.resizeBlock(resize.id, resize.axis, value);
        },

        resetSize(id, axis) {
            this.$wire.resizeBlock(id, axis, null);
        },

        listenForResize(direction) {
            const move = (event) => this.moveResize(event);
            const up = () => this.endResize();

            window.addEventListener('pointermove', move);
            window.addEventListener('pointerup', up);
            window.addEventListener('pointercancel', up);

            this.resizing.off = () => {
                window.removeEventListener('pointermove', move);
                window.removeEventListener('pointerup', up);
                window.removeEventListener('pointercancel', up);
            };

            document.body.dataset.fpbResizing = direction;
        },

        stopResize() {
            this.resizing?.off?.();
            this.resizing?.handle?.removeAttribute('data-active');
            this.resizing = null;
            delete document.body.dataset.fpbResizing;
            this.hideTip();
        },

        startColumnResize(event, id, section, index) {
            event.preventDefault();
            event.stopPropagation();

            const tracks = (section.dataset.fpbTracks ?? '').split('-').map(Number);
            const slots = this.slotsOf(section);

            if (tracks.length !== slots.length || tracks.some((track) => !Number.isFinite(track) || track <= 0)) {
                return;
            }

            const handle = event.currentTarget;

            handle.dataset.active = 'true';
            this.closeMenu();
            this.resizing = {
                kind: 'columns',
                id,
                section,
                index,
                handle,
                startX: event.clientX,
                start: [...tracks],
                tracks: [...tracks],
                total: slots.reduce((sum, el) => sum + el.getBoundingClientRect().width, 0) || 1,
                value: tracks,
            };

            this.listenForResize('x');
        },

        moveColumnResize(event) {
            const resize = this.resizing;
            const pair = resize.start[resize.index] + resize.start[resize.index + 1];
            const share = resize.start[resize.index] + ((event.clientX - resize.startX) / resize.total) * 100;
            const left = Math.round(Math.min(pair - 5, Math.max(5, share)));

            resize.tracks = [...resize.start];
            resize.tracks[resize.index] = left;
            resize.tracks[resize.index + 1] = pair - left;
            resize.value = resize.tracks;
            resize.section.style.gridTemplateColumns = resize.tracks.map((track) => `minmax(0, ${track}fr)`).join(' ');

            const slots = this.slotsOf(resize.section);

            this.placeColumnHandle(resize.handle, this.overlayBox(slots[resize.index]), this.overlayBox(slots[resize.index + 1]));
            this.showTip(resize.tracks.map((track) => `${track}%`).join(' · '), event);
        },

        /* ── Adding blocks ────────────────────────────────── */

        /**
         * Add from the palette, then put the caret in the new block.
         *
         * Picking "Button" after typing into a text block used to leave the new button
         * selected but not in hand: the caret stayed nowhere and the label had to be found
         * and clicked. A new block with text of its own is ready to type into instead.
         */
        insertFromPalette(type) {
            this.afterPaletteInsert();
            this.captureRects();

            return this.$wire.insertBlock(type).then((id) => this.focusEditable(id));
        },

        focusEditable(id) {
            if (!id || this.narrow) {
                return;
            }

            requestAnimationFrame(() => {
                const field = this.blockEl(id)?.querySelector('[data-fpb-field][contenteditable]');

                if (!field) {
                    return;
                }

                field.focus({ preventScroll: true });

                const range = document.createRange();
                const selection = window.getSelection();

                range.selectNodeContents(field);
                range.collapse(false);
                selection?.removeAllRanges();
                selection?.addRange(range);
            });
        },

        /**
         * Point at the inspector field behind the element being typed into.
         */
        revealField(field) {
            if (this.narrow || !field) {
                return;
            }

            this.inspectorTab = 'content';

            this.$nextTick(() => {
                const input = this.$root.querySelector(`.fpb-inspector [id="form.${this.cssEscape(field)}"]`);
                const wrapper = input?.closest('.fi-fo-field') ?? input;

                if (!wrapper) {
                    return;
                }

                wrapper.scrollIntoView({ block: 'nearest', behavior: motion.enabled ? 'smooth' : 'auto' });
                wrapper.classList.remove('fpb-field-flash');
                void wrapper.offsetWidth;
                wrapper.classList.add('fpb-field-flash');
                setTimeout(() => wrapper.classList.remove('fpb-field-flash'), 1400);
            });
        },

        humanise(field) {
            const words = String(field ?? '').replace(/[_-]+/g, ' ').trim();

            return words === '' ? null : words.charAt(0).toUpperCase() + words.slice(1);
        },

        /* ── The right-click menu ─────────────────────────── */

        onContextMenu(event) {
            const target = event.target instanceof Element ? event.target : null;
            const block = target?.closest('.fpb-block');

            // Right-clicking the text being typed keeps the browser's own menu, which is
            // where spelling suggestions live.
            if (!block || (target.closest('[data-fpb-field]') && target.closest('[data-fpb-field]') === document.activeElement)) {
                return;
            }

            event.preventDefault();
            this.openMenuAt(block.dataset.id, event.clientX, event.clientY);
        },

        /** The ⋯ button on a block's bar opens the same menu, for anyone who never right-clicks. */
        openMenu(event, id) {
            const box = event.currentTarget.getBoundingClientRect();

            this.openMenuAt(id, box.right - 8, box.bottom + 4);
        },

        openMenuAt(id, x, y) {
            const block = this.blockEl(id);

            if (!block) {
                return;
            }

            if (this.$wire.selectedId !== id) {
                this.$wire.selectBlock(id);
            }

            this.menu = {
                open: true,
                x,
                y,
                id,
                label: block.querySelector(':scope > .fpb-block-bar .fpb-block-label')?.textContent.trim() ?? '',
                parent: Boolean(block.dataset.parent),
                container: block.dataset.container === 'true',
            };

            this.$nextTick(() => {
                const menu = this.$refs.menu;

                if (!menu) {
                    return;
                }

                const box = menu.getBoundingClientRect();

                this.menu.x = Math.max(8, Math.min(x, window.innerWidth - box.width - 8));
                this.menu.y = Math.max(8, Math.min(y, window.innerHeight - box.height - 8));
                menu.querySelector('.fpb-menu-item')?.focus({ preventScroll: true });
            });
        },

        closeMenu() {
            if (this.menu.open) {
                this.menu.open = false;
            }
        },

        /** Close the menu, then act on the block it was opened for. */
        fromMenu(action) {
            const id = this.menu.id;

            this.closeMenu();

            return id ? action(id, this.blockEl(id)) : null;
        },

        menuInspect(tab) {
            this.fromMenu(() => {
                if (this.narrow) {
                    this.showWorkspace('settings');
                }

                this.inspectorTab = tab;
            });
        },

        menuSelectParent() {
            this.fromMenu((id, el) => el?.dataset.parent && this.$wire.selectBlock(el.dataset.parent));
        },

        menuCopy() {
            this.fromMenu((id) => this.copyBlock(id));
        },

        menuPaste(placement) {
            this.fromMenu((id) => this.clipboard && this.pasteClipboard(this.clipboard, id, placement));
        },

        menuCopyStyle() {
            this.fromMenu((id, el) => this.$wire.copyBlockStyle(id).then((look) => {
                this.styleClipboard = look;
                this.stash('fpb.style-clipboard', JSON.stringify(look));
                motion.flash(el);
                this.say(this.t('notice_style_copied'));
            }));
        },

        menuPasteStyle() {
            this.fromMenu((id) => this.styleClipboard && this.$wire.pasteBlockStyle(id, this.styleClipboard));
        },

        menuAddBelow() {
            this.fromMenu(() => {
                if (this.narrow) {
                    return this.showWorkspace('blocks');
                }

                this.sideTab = 'blocks';
                this.$nextTick(() => this.$refs.paletteSearch?.focus());
            });
        },

        menuDuplicate() {
            this.fromMenu((id) => this.$wire.duplicateBlock(id));
        },

        menuMove(step) {
            this.fromMenu((id) => this.moveSelected(id, step));
        },

        menuResetStyle() {
            this.fromMenu((id) => this.$wire.resetBlockStyle(id));
        },

        menuDelete() {
            this.fromMenu((id, el) => this.remove(id, el?.dataset.hasContent === 'true'));
        },

        keys(letter) {
            return this.isMac ? `⌘${letter}` : `Ctrl+${letter}`;
        },

        /* ── Copy and paste ───────────────────────────────── */

        restoreClipboard() {
            try {
                this.clipboard = window.localStorage.getItem('fpb.clipboard');
                const look = window.localStorage.getItem('fpb.style-clipboard');
                this.styleClipboard = look ? JSON.parse(look) : null;
            } catch {
                // Storage can be off (private mode, a policy); the system clipboard still works.
            }
        },

        stash(key, value) {
            try {
                window.localStorage.setItem(key, value);
            } catch {
                // Not fatal: the copy still lives on the system clipboard for this session.
            }
        },

        hasTextSelection() {
            const selection = window.getSelection?.();

            return Boolean(selection && !selection.isCollapsed && selection.toString().trim() !== '');
        },

        /**
         * Copy a block and everything in it.
         *
         * Written to the system clipboard, so it pastes into another tab or another page,
         * and kept locally, because not every browser lets a page read the clipboard back.
         */
        async copyBlock(id) {
            const json = await this.$wire.copyBlocks(id);

            if (!json) {
                return;
            }

            this.clipboard = json;
            this.stash('fpb.clipboard', json);

            try {
                await navigator.clipboard?.writeText(json);
            } catch {
                // Refused without a fresh click in some browsers; the local copy covers it.
            }

            motion.flash(this.blockEl(id));
            this.say(this.t('notice_copied', { keys: this.keys('V') }));
        },

        onPaste(event) {
            if (this.previewing || this.isTyping(event) || this.narrow) {
                return;
            }

            const text = event.clipboardData?.getData('text/plain') ?? '';
            const payload = text.trim() !== '' ? text : this.clipboard;

            if (!payload) {
                return;
            }

            event.preventDefault();
            this.pasteClipboard(payload, this.$wire.selectedId ?? null, 'after');
        },

        /**
         * Paste a copied block, or anything else: the server decides what the clipboard
         * holds. Copied blocks come back as blocks, HTML as a Custom code block for those
         * allowed one, and plain text as a text block.
         */
        pasteClipboard(payload, targetId, placement) {
            this.captureRects();

            return this.$wire.pasteBlocks(payload, targetId ?? null, placement);
        },

        say(text) {
            this.notice = text;
            clearTimeout(this.noticeTimer);
            this.noticeTimer = setTimeout(() => {
                this.notice = null;
            }, 2600);
        },

        /* ── Preview ──────────────────────────────────────── */

        openPreview() {
            this.flushActiveEditable();
            this.closeMenu();
            this.previewDevice = this.preview;
            this.previewing = true;

            return this.refreshPreview();
        },

        async refreshPreview() {
            const frame = this.$refs.previewFrame;

            if (!frame) {
                return;
            }

            this.previewLoading = true;
            frame.onload = () => {
                this.previewLoading = false;
            };

            try {
                frame.srcdoc = await this.$wire.previewDocument();
            } catch {
                this.previewLoading = false;
            }
        },

        closePreview() {
            this.previewing = false;
            this.previewLoading = false;

            // An empty document stops any video or script the preview left running.
            if (this.$refs.previewFrame) {
                this.$refs.previewFrame.srcdoc = '';
            }

            this.refreshOverlay();
        },
    }));
});

/**
 * Keep Livewire's morph off whatever is being typed into.
 *
 * A render triggered by anything else — selecting a block, an inspector field, a save —
 * would otherwise rewrite the element under the caret with the server's copy of the same
 * text and drop the caret to the end of it.
 */
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.updating', ({ el, skip }) => {
        if (
            el instanceof HTMLElement &&
            el.hasAttribute('data-fpb-field') &&
            (el === document.activeElement || el.contains(document.activeElement))
        ) {
            skip();
        }
    });
});
