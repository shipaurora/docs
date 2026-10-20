@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'canonical' => null,
    'noindex' => false,
    // The page layouts' choices (resources/views/vendor/aurora/layouts): the header's style
    // (standard, floating or logo), and whether the footer and announcement bar show.
    'header' => 'standard',
    'footer' => true,
    'announcement' => true,
    'page' => null,
    'preview' => false,
])

@php
    use Aurora\Models\Setting;
    use Aurora\Support\AssetLibrary;

    $siteName = Setting::get('site_name', config('app.name'));
    $fullTitle = $title ? $title.' · '.$siteName : $siteName;
    $shareImage = AssetLibrary::url($image, 'og') ?? AssetLibrary::url(Setting::get('og_image'), 'og');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    @if ($description)<meta name="description" content="{{ $description }}">@endif
    @if ($canonical)<link rel="canonical" href="{{ $canonical }}">@endif
    {{-- The same page in every language, for search engines. --}}
    @if ($page && ! $noindex)
        @foreach (array_intersect_key(\Aurora\Support\ContentLocale::all(), array_flip($page->availableLocales())) as $code => $language)
            <link rel="alternate" hreflang="{{ $code }}" href="{{ $page->url($code) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ $page->url($page->primaryLocale()) }}">
    @endif
    <meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow' }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}">
    @if ($description)<meta property="og:description" content="{{ $description }}">@endif
    @if ($canonical)<meta property="og:url" content="{{ $canonical }}">@endif
    @if ($shareImage)<meta property="og:image" content="{{ $shareImage }}"><meta name="twitter:card" content="summary_large_image">@endif

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">

    <x-page-builder::styles />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('aurora::partials.brand-palette')

    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $siteName, 'url' => url('/')], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
</head>
<body class="min-h-screen">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:text-zinc-900 focus:shadow-lg">{{ __('Skip to content') }}</a>

    @if ($preview)
        <div class="relative z-[60] flex h-9 items-center justify-center gap-2 bg-amber-400 text-xs font-semibold text-amber-950">
            {{ svg('hero-m-eye', 'size-4') }} {{ __('Preview: this version is not live yet.') }}
        </div>
    @endif

    @if ($announcement)
        <x-site.announcement />
    @endif
    <x-site.header :style="$header" />

    <main id="main" @class(['-mt-20' => $header === 'floating'])>
        {{ $slot }}
    </main>

    @if ($footer)
        <x-site.footer />
    @endif

    @if ($page)
        <x-site.edit-bar :page="$page" />
    @endif

    <x-impersonate::banner />
</body>
</html>
