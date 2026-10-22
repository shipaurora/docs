<?php

use App\Http\Controllers\DocsSearchController;
use Aurora\Http\Middleware\RestrictWebsiteAccess;
use Illuminate\Support\Facades\Route;

// The website's pages (/, every page's address, previews, the sitemap) are Aurora's
// (vendor/shipaurora/cms/routes/pages.php). Routes added here take precedence over them.

// The results under the docs search box, as people type: open to whoever may see the website.
Route::get('docs-search', DocsSearchController::class)
    ->middleware([RestrictWebsiteAccess::class, 'throttle:120,1'])
    ->name('docs.search');
