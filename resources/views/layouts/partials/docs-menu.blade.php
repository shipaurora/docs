{{-- The docs menu's sections, each with its pages; the page being read is marked. --}}
<ul class="space-y-6">
    @foreach ($items as $section)
        <li>
            <a href="{{ $section->url ?? '#' }}" @class([
                'block text-xs font-semibold tracking-wider uppercase transition hover:text-brand-600',
                'text-brand-600' => $isCurrent($section),
                'text-ink' => ! $isCurrent($section),
            ])>{{ $section->title }}</a>

            @if ($section->children->isNotEmpty())
                <ul class="mt-3 space-y-0.5 border-l border-line">
                    @foreach ($section->children as $item)
                        <li>
                            <a href="{{ $item->url ?? '#' }}" @if ($isCurrent($item)) aria-current="page" @endif @class([
                                '-ml-px block border-l py-1.5 pl-4 text-sm transition',
                                'border-brand-600 font-medium text-brand-700' => $isCurrent($item),
                                'border-transparent text-ink-muted hover:border-zinc-300 hover:text-ink' => ! $isCurrent($item),
                            ])>{{ $item->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
