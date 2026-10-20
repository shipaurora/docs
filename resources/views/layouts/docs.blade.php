@php
    /** @var \Aurora\Models\Page $page */
    use Aurora\Models\Menu;

    $description = $page->meta_description ?: $page->excerpt;
    // The docs menu (Settings › Templates › Menu locations: "docs"): each section and its pages.
    $docs = Menu::location('docs');
    $isCurrent = fn ($item): bool => $item->linkable_id === $page->getKey();
    // A search in the docs (?q=, from the search box), shown in place of the page's content.
    // Only on the website itself: the editor's canvas and previews always show the page.
    $docsRoot = \App\Support\DocsSearch::root($page);
    $query = $embedded || $preview ? '' : trim((string) request()->query('q', ''));
    $results = $query === '' ? null : \App\Support\DocsSearch::results($query);
@endphp
{{-- This site's docs layout: a search box and the docs menu beside the page, the menu folded into
     a button on small screens. --}}
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
                <div class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto py-12 pr-2">
                    @include('layouts.partials.docs-search', ['root' => $docsRoot, 'query' => $query, 'id' => 'docs-search'])
                    <nav class="mt-8" aria-label="{{ __('Docs') }}">
                        @include('layouts.partials.docs-menu', ['items' => $docs->menuItems, 'isCurrent' => $isCurrent])
                    </nav>
                </div>
            </aside>
        @endif

        <div class="min-w-0 flex-1">
            <div class="mt-6 lg:hidden">
                @include('layouts.partials.docs-search', ['root' => $docsRoot, 'query' => $query, 'id' => 'docs-search-small'])
            </div>

            @if ($docs)
                <details class="group mt-4 rounded-2xl ring-1 ring-line lg:hidden">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-semibold text-ink">
                        {{ __('Docs menu') }}
                        {{ svg('hero-m-chevron-down', 'size-4 opacity-60 transition group-open:rotate-180') }}
                    </summary>
                    <div class="border-t border-line px-4 py-4">
                        @include('layouts.partials.docs-menu', ['items' => $docs->menuItems, 'isCurrent' => $isCurrent])
                    </div>
                </details>
            @endif

            @if ($results !== null)
                @include('layouts.partials.docs-results', ['results' => $results, 'query' => $query])
            @else
                <div class="-mx-6 lg:mx-0">
                    <x-aurora::blocks :page="$page" />
                </div>
            @endif
        </div>
    </div>
</x-layouts.site>
