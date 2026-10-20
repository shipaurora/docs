{{-- The site's header: 'standard', 'floating' (see-through over the first block) or 'logo' (no menu). --}}
@props(['style' => 'standard', 'offset' => false])

@php
    use Aurora\Models\Menu;
    use Aurora\Models\Setting;

    $menu = $style === 'logo' ? null : Menu::location('header');
    $transparent = $style === 'floating';
    $ctaLabel = Setting::get('header_cta_label');
    $ctaHref = link_href(['cta_type' => 'page', 'cta_page' => Setting::get('header_cta_page')], 'cta');
@endphp

<header
    x-data="{ solid: {{ $transparent ? 'false' : 'true' }}, mobile: false, scrolled: false }"
    x-init="const sync = () => { scrolled = window.scrollY > 12; solid = {{ $transparent ? 'scrolled || mobile' : 'true' }} }; sync(); window.addEventListener('scroll', sync, { passive: true }); $watch('mobile', sync)"
    class="sticky top-0 z-50 transition-all duration-300"
    :class="solid ? 'bg-white/80 backdrop-blur-xl shadow-[0_1px_0_rgb(0_0_0/0.06)]' : 'bg-transparent'"
>
    <nav class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-8 px-6 lg:px-8" aria-label="{{ __('Main') }}">
        <x-site.logo :dark="$transparent" />

        @if ($menu)
            <ul class="hidden items-center gap-1 lg:flex">
                @foreach ($menu->menuItems as $item)
                    {{-- Opens on hover and on keyboard focus; Escape closes it and returns to the item. --}}
                    <li
                        class="relative"
                        x-data="{ open: false }"
                        x-on:mouseenter="open = true"
                        x-on:mouseleave="open = false"
                        x-on:focusin="open = true"
                        x-on:focusout="if (! $el.contains($event.relatedTarget)) open = false"
                        x-on:keydown.escape="open = false; $el.querySelector('a').focus()"
                    >
                        @php $active = $item->isActiveOrHasActiveChild(); @endphp
                        <a
                            href="{{ $item->url ?? '#' }}"
                            @if ($item->target?->value === '_blank') target="_blank" rel="noopener" @endif
                            @if ($item->isActive()) aria-current="page" @endif
                            @if ($item->children->isNotEmpty()) aria-haspopup="true" :aria-expanded="open" @endif
                            class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="solid ? '{{ $active ? 'text-brand-700 bg-brand-50' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-900/5' }}' : '{{ $active ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}'"
                        >
                            {{ $item->title }}
                            @if ($item->children->isNotEmpty())
                                {{ svg('hero-m-chevron-down', 'size-4 opacity-60 transition') }}
                            @endif
                        </a>

                        @if ($item->children->isNotEmpty())
                            <div x-show="open" x-cloak x-transition.origin.top class="absolute top-full left-1/2 w-72 -translate-x-1/2 pt-3">
                                <ul class="rounded-2xl bg-white p-2 shadow-2xl shadow-zinc-900/10 ring-1 ring-zinc-900/5">
                                    @foreach ($item->children as $child)
                                        <li>
                                            <a href="{{ $child->url ?? '#' }}" @if ($child->target?->value === '_blank') target="_blank" rel="noopener" @endif class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-brand-50 hover:text-brand-700">
                                                <span class="grid size-8 place-items-center rounded-lg bg-brand-50 text-brand-600">{{ svg(\Aurora\Support\SiteIcon::name($child->icon) ?? 'hero-o-arrow-right', 'size-4') }}</span>
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="flex items-center gap-3">
            {{-- The same page in the site's other languages. --}}
            @if (($currentPage ?? null) && count($languages = array_intersect_key(\Aurora\Support\ContentLocale::all(), array_flip($currentPage->availableLocales()))) > 1)
                <nav class="flex items-center gap-0.5 rounded-full p-0.5 text-xs font-semibold uppercase ring-1" :class="solid ? 'ring-zinc-900/10' : 'ring-white/25'" aria-label="{{ __('Language') }}">
                    @foreach ($languages as $code => $language)
                        <a
                            href="{{ ($previewing ?? false) ? $currentPage->previewUrl(locale: $code) : $currentPage->url($code) }}"
                            hreflang="{{ $code }}"
                            lang="{{ $code }}"
                            title="{{ $language }}"
                            @if ($code === \Aurora\Support\ContentLocale::current()) aria-current="true" @endif
                            @class([
                                'rounded-full px-2.5 py-1 transition',
                                'bg-brand-600 text-white' => $code === \Aurora\Support\ContentLocale::current(),
                            ])
                            @if ($code !== \Aurora\Support\ContentLocale::current()) :class="solid ? 'text-zinc-600 hover:text-zinc-900' : 'text-white/80 hover:text-white'" @endif
                        >{{ $code }}</a>
                    @endforeach
                </nav>
            @endif

            @if ($ctaLabel && $ctaHref && $style !== 'logo')
                <a href="{{ $ctaHref }}" class="hidden rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-500 sm:inline-flex">{{ $ctaLabel }}</a>
            @endif

            @if ($menu)
                <button type="button" class="grid size-10 place-items-center rounded-full lg:hidden" :class="solid ? 'text-zinc-900 hover:bg-zinc-900/5' : 'text-white hover:bg-white/10'" x-on:click="mobile = ! mobile" :aria-expanded="mobile" aria-label="{{ __('Menu') }}">
                    <span x-show="! mobile">{{ svg('hero-o-bars-3', 'size-6') }}</span>
                    <span x-show="mobile" x-cloak>{{ svg('hero-o-x-mark', 'size-6') }}</span>
                </button>
            @endif
        </div>
    </nav>

    @if ($menu)
        <div x-show="mobile" x-cloak x-collapse class="border-t border-zinc-100 bg-white lg:hidden">
            <ul class="space-y-1 px-6 py-6">
                @foreach ($menu->menuItems as $item)
                    <li>
                        <a href="{{ $item->url ?? '#' }}" class="block rounded-xl px-3 py-2.5 text-base font-semibold text-zinc-900 hover:bg-zinc-50">{{ $item->title }}</a>
                        @foreach ($item->children as $child)
                            <a href="{{ $child->url ?? '#' }}" class="block rounded-xl py-2 pr-3 pl-7 text-sm text-zinc-600 hover:bg-zinc-50">{{ $child->title }}</a>
                        @endforeach
                    </li>
                @endforeach
                @if ($ctaLabel && $ctaHref)
                    <li class="pt-3"><a href="{{ $ctaHref }}" class="block rounded-full bg-brand-600 px-5 py-3 text-center font-semibold text-white">{{ $ctaLabel }}</a></li>
                @endif
            </ul>
        </div>
    @endif
</header>
