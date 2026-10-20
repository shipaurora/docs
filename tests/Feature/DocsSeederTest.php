<?php

namespace Tests\Feature;

use Aurora\Models\ApiKey;
use Aurora\Models\Asset;
use Aurora\Models\AuthenticatorApp;
use Aurora\Models\Form;
use Aurora\Models\Layout;
use Aurora\Models\McpActivity;
use Aurora\Models\Menu;
use Aurora\Models\MenuItem;
use Aurora\Models\NavigationLocation;
use Aurora\Models\Page;
use Aurora\Models\Redirect;
use Aurora\Models\User;
use Aurora\Models\Webhook;
use Database\Seeders\Demo\DocsSeeder;
use Database\Seeders\Demo\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DocsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_again_replaces_the_content_and_keeps_the_account_as_its_owner_set_it_up(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(DocsSeeder::class);

        // Set up as its owner would: a password, an authenticator app and a passkey. And a
        // visitor wrote through the contact form.
        $account = User::query()->sole();
        $account->update(['password' => 'a-strong-password']);
        AuthenticatorApp::factory()->create(['user_id' => $account->getKey(), 'name' => 'iPhone']);
        DB::table('passkeys')->insert(['id' => (string) Str::uuid(), 'authenticatable_id' => $account->getKey(), 'name' => 'MacBook', 'credential_id' => 'abc', 'data' => '{}', 'created_at' => now(), 'updated_at' => now()]);
        $form = Form::query()->sole();
        $form->submissions()->create(['data' => ['name' => 'Kari'], 'status' => 'new']);

        $counts = fn (): array => [
            'pages' => Page::query()->withTrashed()->count(),
            'menus' => Menu::query()->count(),
            'menu items' => MenuItem::query()->count(),
            'assets' => Asset::query()->withTrashed()->count(),
            'files' => count(Storage::disk('public')->allFiles()) + count(Storage::disk('local')->allFiles()),
            'redirects' => Redirect::query()->count(),
            'api keys' => ApiKey::query()->count(),
            'webhooks' => Webhook::query()->count(),
            'ai activity' => McpActivity::query()->count(),
            'menu locations' => NavigationLocation::query()->count(),
            'layouts' => Layout::query()->count(),
        ];
        $before = $counts();

        $this->seed(DocsSeeder::class);

        // The same content, once.
        $this->assertSame($before, $counts());
        $this->get('/')->assertOk();

        // The account as its owner left it.
        $account = User::query()->sole();
        $this->assertTrue(Hash::check('a-strong-password', $account->password));
        $this->assertSame(['Super Admin'], $account->getRoleNames()->all());
        $this->assertSame(1, AuthenticatorApp::query()->where('user_id', $account->getKey())->count());
        $this->assertSame(1, DB::table('passkeys')->where('authenticatable_id', $account->getKey())->count());

        // The contact form is the same one, with what was sent through it.
        $this->assertSame($form->getKey(), Form::query()->sole()->getKey());
        $this->assertSame(1, $form->submissions()->count());
    }

    public function test_the_demo_seeds_and_every_published_page_renders(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(DocsSeeder::class);

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
        $account = User::query()->sole();
        $this->assertSame(UserSeeder::email(), $account->email);
        $this->assertSame([$account->id], Page::query()->distinct()->pluck('author_id')->all());
    }
}
