<?php

namespace Database\Seeders\Demo;

use Aurora\Database\Seeders\EssentialSeeder;
use Aurora\Support\PageCache;
use Illuminate\Database\Seeder;

class DocsSeeder extends Seeder
{
    public function run(): void
    {
        // The docs website's content: what every installation has, plus Norwegian, its one
        // account, its pages, media, the contact form, menus and settings. It can run again
        // (php artisan db:seed on a deploy): the content is replaced, and the accounts, with
        // their passwords, passkeys and authenticator apps, stay as they are.
        $this->call([
            ResetContentSeeder::class,
            EssentialSeeder::class,
            LocaleSeeder::class,
            UserSeeder::class,
            PageSeeder::class,
            SettingsSeeder::class,
            MenuSeeder::class,
        ]);

        // Visitors see the new content straight away, not the pages cached before.
        PageCache::flush();
    }
}
