{{-- This site's page while it's open only to some addresses (Aurora's, published and made the
     site's own), styled like its password page. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ \Aurora\Models\Setting::get('site_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css'])
    @include('aurora::partials.brand-palette')
</head>
<body class="tone-light grid min-h-screen place-items-center bg-zinc-50 px-6 py-16 font-body text-zinc-600 antialiased">
    <main class="w-full">
        <div class="mx-auto max-w-md rounded-3xl bg-white p-6 shadow-xl shadow-zinc-900/5 ring-1 ring-zinc-900/5 sm:p-8">
            <span class="grid size-12 place-items-center rounded-full bg-brand-50 text-brand-600 ring-1 ring-brand-200">{{ svg('hero-o-clock', 'size-6') }}</span>
            <h1 class="mt-5 font-display text-2xl font-semibold text-zinc-950">{{ __('This website isn’t open yet') }}</h1>
            <p class="mt-2 text-zinc-600">{{ __('It’s only available from certain places for now. Please come back later.') }}</p>
        </div>
    </main>
</body>
</html>
