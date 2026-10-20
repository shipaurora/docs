@props([])

@php
    use Aurora\Models\Setting;
    $announcement = Setting::get('announcement', []);
@endphp

@if (($announcement['enabled'] ?? false) && filled($announcement['text'] ?? null))
    <div x-data="{ open: true }" x-show="open" x-collapse class="relative z-[55] bg-zinc-950 text-sm text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-center gap-3 px-10 py-2.5">
            <span class="hidden rounded-full bg-gradient-to-r from-brand-500 to-accent-400 px-2 py-0.5 text-[11px] font-bold tracking-wide uppercase sm:inline">{{ __('New') }}</span>
            <p class="truncate">{{ $announcement['text'] }}</p>
            @if (filled($announcement['url'] ?? null))
                <a href="{{ \Aurora\Support\SafeUrl::href($announcement['url'] ?? null) ?? '#' }}" class="shrink-0 font-semibold text-brand-300 hover:text-white">{{ $announcement['link_label'] ?? __('Read more') }} →</a>
            @endif
            <button type="button" x-on:click="open = false" class="absolute right-3 rounded p-1 text-white/60 hover:text-white" aria-label="{{ __('Dismiss') }}">{{ svg('hero-m-x-mark', 'size-4') }}</button>
        </div>
    </div>
@endif
