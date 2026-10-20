<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * The website's own set-up. The CMS's is Aurora's (Aurora\Providers\AuroraServiceProvider);
 * the site's blocks and their header and footer settings are BlocksServiceProvider's.
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }
}
