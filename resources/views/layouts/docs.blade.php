@php
    /** @var \Aurora\Models\Page $page */
    use Aurora\Models\Menu;

    $description = $page->meta_description ?: $page->excerpt;
    // The docs menu (Settings › Templates › Menu locations: "docs"): each section and its pages.
    $docs = Menu::location('docs');
    $isCurrent = fn ($item): bool => $item->linkable_id === $page->getKey();
@endphp
{{-- This site's docs layout: the docs menu beside the page, folded into a button on small screens. --}}
<x-layouts.site
    :title="$page->seoTitle()"
    :description="$description"
    :image="$page->og_image"
    :canonical="$page->url()"
    :noindex="$page->noindex || $page->isRestricted() || $preview || $embedded"
    :page="$embedded ? null : $page"
    :preview="$preview"
>
    <div class="mx-auto flex max-w-7xl gap-10 px-6 lg:px-8">
        @if ($docs)
            <aside class="hidden w-64 shrink-0 lg:block">
                <nav class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto py-12 pr-2" aria-label="{{ __('Docs') }}">
                    @include('layouts.partials.docs-menu', ['items' => $docs->menuItems, 'isCurrent' => $isCurrent])
                </nav>
            </aside>
        @endif

        <div class="min-w-0 flex-1">
            @if ($docs)
                <details class="group mt-6 rounded-2xl ring-1 ring-line lg:hidden">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-semibold text-ink">
                        {{ __('Docs menu') }}
                        {{ svg('hero-m-chevron-down', 'size-4 opacity-60 transition group-open:rotate-180') }}
                    </summary>
                    <div class="border-t border-line px-4 py-4">
                        @include('layouts.partials.docs-menu', ['items' => $docs->menuItems, 'isCurrent' => $isCurrent])
                    </div>
                </details>
            @endif

            <div class="-mx-6 lg:mx-0">
                <x-aurora::blocks :page="$page" />
            </div>
        </div>
    </div>
</x-layouts.site>
