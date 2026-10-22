{{-- The docs pages a search found (App\Support\DocsSearch), in place of the page's own content. --}}
<section class="py-12" aria-labelledby="docs-search-heading">
    <p class="text-sm font-semibold tracking-wider text-brand-600 uppercase">{{ __('Search the docs') }}</p>
    <h1 id="docs-search-heading" class="mt-2 font-display text-3xl font-semibold tracking-tight text-ink">
        {{ $results->isEmpty() ? __('Nothing in the docs matches “:query”', ['query' => $query]) : trans_choice('{1} One page for “:query”|[2,*] :count pages for “:query”', $results->count(), ['query' => $query]) }}
    </h1>

    @if ($results->isEmpty())
        <p class="mt-4 text-ink-muted">{{ __('Try fewer or other words, or browse the docs from the menu.') }}</p>
    @else
        <ol class="mt-8 divide-y divide-line border-y border-line">
            @foreach ($results as $result)
                <li>
                    <a href="{{ $result['page']->url() }}" class="group block py-5">
                        @if (filled($result['section']))
                            <span class="text-xs font-semibold tracking-wider text-ink-muted uppercase">{{ $result['section'] }}</span>
                        @endif
                        <span class="mt-1 block text-lg font-semibold text-ink group-hover:text-brand-600 [&_mark]:rounded [&_mark]:bg-brand-100 [&_mark]:px-0.5 [&_mark]:text-inherit">{{ $result['title'] }}</span>
                        <span class="mt-1 block text-sm leading-6 text-ink-muted [&_mark]:rounded [&_mark]:bg-brand-100 [&_mark]:px-0.5 [&_mark]:text-ink">{{ $result['snippet'] }}</span>
                    </a>
                </li>
            @endforeach
        </ol>
    @endif
</section>
