@php
    use Aurora\Models\Menu;
    use Aurora\Models\Setting;

    $footer = Menu::location('footer');
    $legal = Menu::location('legal');
    $socials = array_filter((array) Setting::get('socials', []), fn ($social) => filled($social['url'] ?? null));
@endphp

<footer class="tone-dark relative isolate overflow-hidden bg-zinc-950">
    <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-80 w-[60rem] -translate-x-1/2 rounded-full bg-brand-600/20 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-6 pt-20 pb-10 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.2fr_2fr]">
            <div class="max-w-sm">
                <div x-data="{ solid: false }"><x-site.logo :dark="true" /></div>
                <p class="mt-5 text-sm/6 text-white/60">{{ Setting::get('tagline') }}</p>
                @if ($socials)
                    <div class="mt-6 flex gap-2">
                        @foreach ($socials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-white/5 text-xs font-semibold text-white/70 ring-1 ring-white/10 transition hover:bg-white/10 hover:text-white" aria-label="{{ $social['network'] ?? __('Social') }}">
                                {{ mb_substr($social['network'] ?? '?', 0, 2) }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($footer)
                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                    @foreach ($footer->menuItems as $column)
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $column->title }}</p>
                            <ul class="mt-4 space-y-3">
                                @foreach ($column->children as $link)
                                    <li><a href="{{ $link->url ?? '#' }}" @if ($link->target?->value === '_blank') target="_blank" rel="noopener" @endif class="text-sm text-white/60 transition hover:text-white">{{ $link->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-16 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ Setting::get('site_name', config('app.name')) }}. {{ __('All rights reserved.') }} {{ Setting::get('footer_note') }}</p>
            @if ($legal)
                <ul class="flex flex-wrap gap-6">
                    @foreach ($legal->menuItems as $link)
                        <li><a href="{{ $link->url ?? '#' }}" class="hover:text-white">{{ $link->title }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</footer>
