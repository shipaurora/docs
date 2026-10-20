<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * A block's picture (resources/block-previews/{type}.webp), straight from the site's
 * resources: nothing to publish. For the visual editor's block list, so only for people
 * who may edit pages. Cached by their browser for a long time; its address changes with the
 * file.
 */
class BlockPreviewController
{
    public function __invoke(Request $request, string $type): BinaryFileResponse
    {
        // Signed in, with the permission to edit pages (guests are refused too).
        Gate::authorize('update Page');

        $file = resource_path("block-previews/{$type}.webp");

        abort_unless(is_file($file), 404);

        return response()->file($file, ['Content-Type' => 'image/webp', 'Cache-Control' => 'private, max-age=31536000, immutable']);
    }
}
