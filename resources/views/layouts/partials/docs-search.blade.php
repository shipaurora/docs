{{-- Search in the docs: the first results open underneath as people type (resources/js/docs-search.js);
     Enter goes to the docs overview page, which shows them all (App\Support\DocsSearch). --}}
<form
    method="get"
    action="{{ $root->url() }}"
    role="search"
    class="relative"
    x-data="docsSearch(@js(route('docs.search')), @js(\Aurora\Support\ContentLocale::current()))"
    x-on:click.outside="close()"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
>
    <label for="{{ $id }}" class="sr-only">{{ __('Search the docs') }}</label>
    {{ svg('hero-m-magnifying-glass', 'pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-muted') }}
    <input
        id="{{ $id }}"
        x-ref="input"
        type="search"
        name="q"
        value="{{ $query }}"
        placeholder="{{ __('Search the docs') }}"
        autocomplete="off"
        role="combobox"
        aria-autocomplete="list"
        aria-controls="{{ $id }}-results"
        aria-expanded="false"
        x-bind:aria-expanded="open.toString()"
        x-bind:aria-activedescendant="active >= 0 ? '{{ $id }}-result-' + active : null"
        x-model="query"
        x-on:input.debounce.150ms="search()"
        x-on:focus="reopen()"
        x-on:keydown.arrow-down.prevent="move(1)"
        x-on:keydown.arrow-up.prevent="move(-1)"
        x-on:keydown.enter="choose($event)"
        x-on:keydown.escape="if (open) { $event.preventDefault(); close() }"
        class="w-full rounded-xl bg-white py-2 pr-3 pl-9 text-sm text-ink ring-1 ring-line placeholder:text-ink-muted focus:ring-2 focus:ring-brand-600 focus:outline-none"
    >

    <div
        id="{{ $id }}-results"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.100ms
        class="absolute inset-x-0 top-full z-50 mt-2 overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-line lg:right-auto lg:w-104"
    >
        <ul role="listbox" aria-label="{{ __('Search the docs') }}" class="max-h-[min(28rem,70vh)] overflow-y-auto p-1.5">
            <template x-for="(result, index) in results" x-bind:key="result.url">
                <li
                    role="option"
                    x-bind:id="'{{ $id }}-result-' + index"
                    x-bind:aria-selected="(index === active).toString()"
                >
                    <a
                        x-bind:href="result.url"
                        tabindex="-1"
                        x-on:mouseenter="active = index"
                        x-bind:class="index === active ? 'bg-brand-50' : ''"
                        class="block rounded-xl px-3 py-2.5 [&_mark]:rounded [&_mark]:bg-brand-100 [&_mark]:px-0.5 [&_mark]:text-inherit"
                    >
                        <span x-show="result.section" x-text="result.section" class="block text-[11px] font-semibold tracking-wider text-ink-muted uppercase"></span>
                        <span x-html="result.title" class="block text-sm font-semibold text-ink"></span>
                        <span x-html="result.snippet" class="mt-0.5 line-clamp-2 text-xs leading-5 text-ink-muted"></span>
                    </a>
                </li>
            </template>
        </ul>

        <p x-show="results.length === 0" class="px-4 py-3 text-sm text-ink-muted" x-text="@js(__('Nothing in the docs matches “:query”')).replace(':query', query.trim())"></p>

        <a
            x-show="more"
            x-bind:href="allResultsUrl()"
            class="block border-t border-line px-4 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-50"
        >{{ __('See all results') }}</a>
    </div>
</form>
