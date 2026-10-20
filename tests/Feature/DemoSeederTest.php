<?php

namespace Tests\Feature;

use Aurora\Models\Page;
use Aurora\Models\User;
use Database\Seeders\Demo\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_demo_seeds_and_every_published_page_renders(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(DemoSeeder::class);

        $pages = Page::query()->live()->get();
        $this->assertNotEmpty($pages);

        // In every language each page is written in.
        foreach ($pages as $page) {
            foreach ($page->availableLocales() as $locale) {
                $this->get($page->url($locale))->assertOk();
            }
        }

        $this->get('/sitemap.xml')->assertOk();

        // The pages list (latest edited first) opens with Home.
        $this->assertTrue(Page::query()->latest('updated_at')->first()->is_homepage);

        // One person wrote it all: every page is theirs.
        $thomas = User::query()->sole();
        $this->assertSame('thomas@alrek.no', $thomas->email);
        $this->assertSame([$thomas->id], Page::query()->distinct()->pluck('author_id')->all());
    }
}
