<?php

namespace Database\Seeders;

use Database\Seeders\Demo\DemoSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * This site is Aurora's demo: its content comes with the package.
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}
