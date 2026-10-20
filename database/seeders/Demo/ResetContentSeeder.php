<?php

namespace Database\Seeders\Demo;

use Aurora\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clears the content this website seeds, so seeding again (php artisan db:seed on a deploy)
 * replaces it instead of adding a second copy: the pages with their revisions and redirects,
 * the menus, and the media library with its files. What people set up stays as it is: their
 * accounts, passwords, passkeys, authenticator apps and sessions, the roles, the labels, and
 * the contact form with what visitors sent through it (the other seeders update those in
 * place). On an empty database there's nothing to clear.
 */
class ResetContentSeeder extends Seeder
{
    /**
     * The tables of seeded content, in the order they're cleared: what points at a page or a
     * menu before the page or the menu.
     *
     * @var array<int, string>
     */
    protected const TABLES = ['menu_items', 'menu_locations', 'menus', 'asset_usages', 'page_revisions', 'redirects', 'pages', 'asset_asset_tag', 'asset_tags', 'asset_folders'];

    public function run(): void
    {
        // Each asset deleted for good, so its file and its renditions go from the disk too.
        Asset::query()->withTrashed()->each(fn (Asset $asset) => $asset->forceDelete());

        Schema::withoutForeignKeyConstraints(function (): void {
            foreach (self::TABLES as $table) {
                DB::table($table)->delete();
            }
        });
    }
}
