<?php

namespace App\Providers;

use Aurora\Support\Demo;
use Database\Seeders\Demo\UserSeeder;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\ServiceProvider;

/**
 * The website's own set-up. The CMS's is Aurora's (Aurora\Providers\AuroraServiceProvider);
 * the site's blocks and their header and footer settings are BlocksServiceProvider's.
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Aurora is developed in this repository (packages/cms, packages/aurora):
        // `composer dev` rebuilds its admin theme and scripts whenever they change.
        DevCommands::node('dev:aurora', 'aurora');

        // The account demo mode (AURORA_DEMO_LOGIN) signs in as with one click.
        Demo::register(UserSeeder::accounts());
    }
}
