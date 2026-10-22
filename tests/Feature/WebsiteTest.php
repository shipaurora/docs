<?php

namespace Tests\Feature;

use Aurora\Blocks\Blocks;
use Aurora\Enums\PageStatus;
use Aurora\Filament\Resources\Pages\PageResource;
use Aurora\Models\Page;
use Aurora\Models\Setting;
use Aurora\Models\Site;
use Aurora\Models\User;
use Aurora\Providers\AuroraServiceProvider;
use Aurora\Support\CurrentSite;
use Aurora\Support\Themes;
use Database\Seeders\Demo\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use Tests\TestCase;

/**
 * What this website adds to Aurora: its layout (the preview banner, the language switcher and
 * the edit bar), its own password page, the screenshots of its blocks and its second admin
 * theme. Aurora itself is tested in its own repository.
 */
class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_preview_says_so_and_keeps_its_language_switcher(): void
    {
        $this->seed(LocaleSeeder::class);
        $draft = Page::factory()->create(['title' => 'Draft']);
        $this->get($draft->previewUrl())->assertOk()->assertSee('Preview');

        $shop = Site::query()->create(['name' => 'Shop', 'handle' => 'shop', 'domain' => 'shop.example.com', 'default_locale' => 'nb', 'settings' => ['site_name' => 'Fjordbutikken']]);
        $page = CurrentSite::using($shop, fn () => Page::factory()->create(['title' => 'Sommerjobb', 'status' => PageStatus::Draft]));

        $preview = $this->get($page->previewUrl())->assertOk()->assertSee('Fjordbutikken');

        // The site may have no address of its own: other languages stay in the preview.
        $this->assertMatchesRegularExpression('#href="[^"]*/preview/'.$page->id.'\?[^"]*locale=en#', $preview->getContent());
    }

    public function test_people_who_may_edit_a_page_get_a_way_into_the_editor_from_it(): void
    {
        $page = Page::factory()->homepage()->create(['title' => 'Home']);

        $this->get('/')->assertOk()->assertDontSee('Edit page');

        $this->actingAs($this->userWithRole(User::SUPER_ADMIN))
            ->get('/')
            ->assertOk()
            ->assertSee('Edit page')
            ->assertSee(PageResource::settingsUrl($page), false);
    }

    public function test_the_password_page_is_the_website_s_own(): void
    {
        $this->assertContains(
            resource_path('views/vendor/aurora/page-password.blade.php'),
            ServiceProvider::pathsToPublish(AuroraServiceProvider::class, 'aurora-views'),
        );
        $this->assertSame(resource_path('views/vendor/aurora/page-password.blade.php'), view('aurora::page-password')->getPath());
    }

    public function test_the_block_library_offers_a_screenshot_of_each_of_its_blocks(): void
    {
        $hero = Blocks::find('hero')::preview();
        $this->assertStringContainsString('/aurora/blocks/hero.webp', $hero);

        // Only for people who may edit pages: not visitors, nor viewers.
        $this->get($hero)->assertForbidden();
        $this->actingAs($this->userWithRole('Viewer'))->get($hero)->assertForbidden();

        $this->actingAs($this->userWithRole('Author'))
            ->get($hero)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');
        $this->get('/aurora/blocks/nope.webp')->assertNotFound();
    }

    public function test_the_minimal_theme_is_one_more_to_pick(): void
    {
        $this->assertEqualsCanonicalizing(['borealis', 'minimal'], array_keys(Themes::all()));
        $this->assertSame(['light', 'dark'], Themes::all()['minimal']['modes']);
        $this->assertFileExists(Themes::all()['minimal']['previews']['dark']);

        Setting::put([Themes::SETTING => 'minimal']);
        $this->assertSame('minimal', Themes::active());
        $this->assertStringContainsString('/css/shipaurora/minimal-theme/minimal.css', Themes::html());
    }
}
