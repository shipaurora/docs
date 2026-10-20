@php
    /** @var \Aurora\Models\Page $page */
    $description = $page->meta_description ?: $page->excerpt;
@endphp
{{-- This site's standard layout (Aurora's, published and made its own): header, menu and footer. --}}
<x-layouts.site
    :title="$page->seoTitle()"
    :description="$description"
    :image="$page->og_image"
    :canonical="$page->url()"
    :noindex="$page->noindex || $page->isRestricted() || $preview || $embedded"
    :page="$embedded ? null : $page"
    :preview="$preview"
>
    <x-aurora::blocks :page="$page" />
</x-layouts.site>
