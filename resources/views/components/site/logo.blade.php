@props(['dark' => false])

@php $name = \Aurora\Models\Setting::get('site_name', config('app.name')); @endphp

<a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="{{ __(':name home', ['name' => $name]) }}">
    {{-- Aurora's own mark, the same as the admin's (public/brand-mark.svg). --}}
    <img src="{{ asset('brand-mark.svg') }}" alt="" class="size-8 shrink-0 rounded-[9px]" />
    <span class="font-display text-xl font-semibold tracking-tight" :class="solid ? 'text-zinc-950' : '{{ $dark ? 'text-white' : 'text-zinc-950' }}'">{{ $name }}</span>
</a>
