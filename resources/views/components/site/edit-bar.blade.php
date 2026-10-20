@props(['page'])

{{-- Visible only to people who may edit this page: a shortcut from the live page
     straight into the visual editor. --}}
@can('update', $page)
    <div class="fixed bottom-5 left-1/2 z-[70] -translate-x-1/2" x-data="{ hidden: false }" x-show="! hidden">
        <div class="flex items-center gap-1 rounded-full bg-zinc-950/90 p-1.5 pl-4 text-sm text-white shadow-2xl ring-1 ring-white/10 backdrop-blur">
            <span class="mr-2 hidden items-center gap-2 text-white/70 sm:flex">
                <span @class(['size-2 rounded-full', 'bg-emerald-400' => $page->isLive(), 'bg-amber-400' => ! $page->isLive()])></span>
                {{ $page->isLive() ? __('Live') : $page->status->getLabel() }}
            </span>
            <a href="{{ \Aurora\Filament\Resources\Pages\PageResource::editorUrl($page) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 font-semibold text-zinc-900 transition hover:bg-brand-50">
                {{ svg('hero-m-paint-brush', 'size-4') }} {{ __('Edit page') }}
            </a>
            <a href="{{ \Aurora\Filament\Resources\Pages\PageResource::settingsUrl($page) }}" class="grid size-9 place-items-center rounded-full text-white/70 hover:bg-white/10 hover:text-white" title="{{ __('Page settings') }}">
                {{ svg('hero-m-adjustments-horizontal', 'size-4') }}
            </a>
            <button type="button" class="grid size-9 place-items-center rounded-full text-white/50 hover:bg-white/10 hover:text-white" x-on:click="hidden = true" aria-label="{{ __('Hide') }}">
                {{ svg('hero-m-x-mark', 'size-4') }}
            </button>
        </div>
    </div>
@endcan
