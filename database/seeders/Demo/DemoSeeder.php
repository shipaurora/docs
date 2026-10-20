<?php

namespace Database\Seeders\Demo;

use Aurora\Database\Seeders\EssentialSeeder;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // The docs website's content: what every installation has, plus Norwegian, its one
        // account, its pages, media, the contact form, menus and settings.
        $this->call([
            EssentialSeeder::class,
            LocaleSeeder::class,
            UserSeeder::class,
            PageSeeder::class,
            SettingsSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
