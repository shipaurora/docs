@php
    /** @var \Aurora\Models\Page $page */
    $error = session('errors')?->getBag('page_password')->first('password');
@endphp
{{-- This site's password page for password-protected pages (Aurora's, published and made the
     site's own): a page of its own, without the site's header and footer, styled like its form
     block. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $page->seoTitle() }} · {{ \Aurora\Models\Setting::get('site_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css'])
    @include('aurora::partials.brand-palette')
</head>
<body class="tone-light grid min-h-screen place-items-center bg-zinc-50 px-6 py-16 font-body text-zinc-600 antialiased">
    <main class="w-full">
        <div class="mx-auto max-w-md rounded-3xl bg-white p-6 shadow-xl shadow-zinc-900/5 ring-1 ring-zinc-900/5 sm:p-8">
            <span class="grid size-12 place-items-center rounded-full bg-brand-50 text-brand-600 ring-1 ring-brand-200">{{ svg('hero-o-lock-closed', 'size-6') }}</span>
            <h1 class="mt-5 font-display text-2xl font-semibold text-zinc-950">{{ __('This page is password protected') }}</h1>
            <p class="mt-2 text-zinc-600">{{ __('Enter the password to see it.') }}</p>

            <form method="POST" action="{{ route('pages.unlock', $page) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="page-password" class="mb-1.5 block text-sm font-medium text-zinc-900">{{ __('Password') }}</label>
                    <input
                        id="page-password"
                        type="password"
                        name="password"
                        required
                        autofocus
                        autocomplete="current-password"
                        @if ($error) aria-invalid="true" aria-describedby="page-password-error" @endif
                        @class([
                            'block w-full rounded-xl border-0 bg-white px-4 py-3 text-zinc-900 shadow-sm ring-1 ring-inset placeholder:text-zinc-400 focus:ring-2 focus:ring-inset',
                            'ring-zinc-300 focus:ring-brand-500' => ! $error,
                            'ring-rose-400 focus:ring-rose-500' => $error,
                        ])
                    >
                    @if ($error)
                        <p id="page-password-error" role="alert" class="mt-1.5 text-sm text-rose-600">{{ $error }}</p>
                    @endif
                </div>
                <button type="submit" class="w-full rounded-full bg-brand-600 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">{{ __('Show the page') }}</button>
            </form>
        </div>
    </main>
</body>
</html>
