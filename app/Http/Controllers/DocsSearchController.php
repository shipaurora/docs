<?php

namespace App\Http\Controllers;

use App\Support\DocsSearch;
use Aurora\Support\ContentLocale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /docs-search?q=…&locale=…: the first docs pages a search finds, as JSON, for the
 * results that open under the docs search box while people type. The same search as the
 * results page (DocsSearch), in the language of the page it's typed on, the searched words
 * marked in each title and snippet.
 */
class DocsSearchController
{
    /**
     * How many results open under the search box; the results page has the rest.
     */
    public const SHOWN = 6;

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'locale' => ['nullable', 'string', 'max:10'],
        ]);

        ContentLocale::set($validated['locale'] ?? null);

        $results = DocsSearch::results((string) ($validated['q'] ?? ''));

        return response()->json([
            'results' => $results->take(self::SHOWN)->map(fn (array $result): array => [
                'url' => $result['page']->url(),
                'title' => $result['title']->toHtml(),
                'section' => $result['section'],
                'snippet' => $result['snippet']->toHtml(),
            ])->values(),
            'more' => $results->count() > self::SHOWN,
        ]);
    }
}
