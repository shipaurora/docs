{{-- Search in the docs: to the docs overview page, which shows the results (App\Support\DocsSearch). --}}
<form method="get" action="{{ $root->url() }}" role="search" class="relative">
    <label for="{{ $id }}" class="sr-only">{{ __('Search the docs') }}</label>
    {{ svg('hero-m-magnifying-glass', 'pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-muted') }}
    <input
        id="{{ $id }}"
        type="search"
        name="q"
        value="{{ $query }}"
        placeholder="{{ __('Search the docs') }}"
        autocomplete="off"
        class="w-full rounded-xl bg-white py-2 pr-3 pl-9 text-sm text-ink ring-1 ring-line placeholder:text-ink-muted focus:ring-2 focus:ring-brand-600 focus:outline-none"
    >
</form>
