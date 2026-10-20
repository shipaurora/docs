<?php

namespace Database\Seeders;

use Database\Seeders\Demo\DocsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * This website's content (DocsSeeder). Run it again on a deploy (php artisan db:seed --force)
     * to replace the content with what's in the code; the accounts stay as they are.
     */
    public function run(): void
    {
        $this->call(DocsSeeder::class);
    }
}
