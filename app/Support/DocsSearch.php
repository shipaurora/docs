<?php

namespace App\Support;

use Aurora\Models\Page;
use Aurora\Support\ContentLocale;
use Aurora\Support\PageSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Search in the docs: the website's own search (Page::search: the search index, this site,
 * only what visitors may read), narrowed to the pages on the docs layout, so the guides and
 * the product pages stay out of it. A page is found for its own words: the cards linking to
 * other pages (the previous and next page under every doc, the overview's list) describe
 * those pages, not this one, and don't count.
 */
class DocsSearch
{
    /**
     * The layout the docs pages are drawn with: what makes a page part of the docs.
     */
    public const LAYOUT = 'docs';

    /**
     * The most results shown for one search.
     */
    public const LIMIT = 20;

    /**
     * How many matches to ask the search index for, before leaving out the pages that only
     * match through their cards.
     */
    protected const CANDIDATES = 50;

    /**
     * The docs pages matching a search, most relevant first, each with its title and a few
     * words around the first match, the searched words marked in both, and the section it's in.
     *
     * @return Collection<int, array{page: Page, title: HtmlString, section: ?string, snippet: HtmlString}>
     */
    public static function results(string $query): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return collect();
        }

        $words = static::words($query);
        $locale = ContentLocale::current();

        return Page::search($query)
            ->query(fn (Builder $pages) => $pages
                ->whereHas('layout', fn (Builder $layout) => $layout->where('handle', self::LAYOUT))
                ->with('parent'))
            ->take(self::CANDIDATES)
            ->get()
            ->map(fn (Page $page): array => ['page' => $page, 'text' => static::ownText($page, $locale)])
            ->filter(fn (array $match): bool => static::mentionsAll($match['text'], $words))
            ->take(self::LIMIT)
            ->map(fn (array $match): array => [
                'page' => $match['page'],
                'title' => static::highlight($match['page']->title, $words),
                'section' => $match['page']->parent?->title,
                'snippet' => static::snippet($match['text'], $words),
            ])
            ->values();
    }

    /**
     * Where the docs start (the overview page): the topmost page on the docs layout above
     * this one, where a search from any docs page goes.
     */
    public static function root(Page $page): Page
    {
        foreach ([...$page->ancestors(), $page] as $candidate) {
            if ($candidate->layout?->handle === self::LAYOUT) {
                return $candidate;
            }
        }

        return $page;
    }

    /**
     * A few words of a page's text around the first match, escaped, with every searched word
     * marked.
     *
     * @param  array<int, string>  $words
     */
    public static function snippet(string $text, array $words, int $length = 180): HtmlString
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        $first = null;

        foreach ($words as $word) {
            $found = mb_stripos($text, $word);

            if ($found !== false && ($first === null || $found < $first)) {
                $first = $found;
            }
        }

        // Start a little before the match, at the beginning of a word.
        $start = max(0, ($first ?? 0) - 60);

        if ($start > 0 && ($space = mb_strpos($text, ' ', $start)) !== false && $space < ($first ?? $start) + 1) {
            $start = $space + 1;
        }

        $excerpt = mb_substr($text, $start, $length);
        $html = static::highlight($excerpt, $words)->toHtml();

        return new HtmlString(($start > 0 ? '… ' : '').$html.($start + mb_strlen($excerpt) < mb_strlen($text) ? ' …' : ''));
    }

    /**
     * A text, escaped, with every searched word in it marked.
     *
     * @param  array<int, string>  $words
     */
    public static function highlight(string $text, array $words): HtmlString
    {
        $html = e($text);

        if ($words !== []) {
            $pattern = '/('.implode('|', array_map(fn (string $word): string => preg_quote(e($word), '/'), $words)).')/iu';
            $html = (string) preg_replace($pattern, '<mark>$1</mark>', $html);
        }

        return new HtmlString($html);
    }

    /**
     * A page's own searchable text in a language: as the search index has it, without the
     * cards that link to other pages.
     */
    protected static function ownText(Page $page, string $locale): string
    {
        $own = $page->replicate();
        $own->blocks = static::withoutPageCards($page->blocks ?? []);

        return PageSearch::textIn($own, $locale);
    }

    /**
     * Blocks without the cards that link to a page, nested blocks included.
     *
     * @param  array<int, mixed>  $blocks
     * @return array<int, mixed>
     */
    protected static function withoutPageCards(array $blocks): array
    {
        $kept = [];

        foreach ($blocks as $key => $block) {
            if (is_array($block) && ($block['type'] ?? null) === 'card' && filled($block['data']['link_page'] ?? null)) {
                continue;
            }

            if (is_array($block)) {
                foreach (['children', 'slots', 'blocks'] as $nested) {
                    if (isset($block[$nested]) && is_array($block[$nested])) {
                        $block[$nested] = array_is_list($block[$nested])
                            ? static::withoutPageCards($block[$nested])
                            : array_map(fn (mixed $slot): mixed => is_array($slot) ? static::withoutPageCards($slot) : $slot, $block[$nested]);
                    }
                }
            }

            $kept[$key] = $block;
        }

        return array_is_list($blocks) ? array_values($kept) : $kept;
    }

    /**
     * Whether a text has every word searched for.
     *
     * @param  array<int, string>  $words
     */
    protected static function mentionsAll(string $text, array $words): bool
    {
        foreach ($words as $word) {
            if (mb_stripos($text, $word) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * The words searched for, without punctuation.
     *
     * @return array<int, string>
     */
    protected static function words(string $query): array
    {
        return preg_split('/[^\pL\pN]+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
