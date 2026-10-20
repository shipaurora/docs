<?php

namespace Tests\Feature;

use Aurora\Database\Seeders\RolesAndPermissionsSeeder;
use Aurora\Models\User;
use Database\Seeders\Demo\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The demo sign-in route only exists with AURORA_DEMO_LOGIN on, and routes are registered
     * when the application boots: switch it on before that.
     */
    protected function setUp(): void
    {
        $this->setDemoLogin('true');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->setDemoLogin('false');
    }

    protected function setDemoLogin(string $value): void
    {
        putenv('AURORA_DEMO_LOGIN='.$value);
        $_ENV['AURORA_DEMO_LOGIN'] = $_SERVER['AURORA_DEMO_LOGIN'] = $value;
    }

    public function test_a_seeded_demo_account_signs_in_with_one_click(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, UserSeeder::class]);

        $this->get(route('demo.login', UserSeeder::email()))->assertRedirect('/admin');

        $this->assertAuthenticatedAs(User::query()->where('email', UserSeeder::email())->sole());
    }

    public function test_it_signs_in_as_no_one_but_the_seeded_demo_accounts(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, UserSeeder::class]);
        User::factory()->create(['email' => 'boss@example.com']);
        User::query()->where('email', UserSeeder::email())->update(['is_active' => false]);

        $this->get(route('demo.login', 'boss@example.com'))->assertNotFound();
        $this->get(route('demo.login', UserSeeder::email()))->assertNotFound();
        $this->assertGuest();
    }

    public function test_it_only_returns_to_addresses_on_this_site(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, UserSeeder::class]);

        $this->get(route('demo.login', ['email' => UserSeeder::email(), 'to' => '/admin/main/pages']))->assertRedirect('/admin/main/pages');
        $this->get(route('demo.login', ['email' => UserSeeder::email(), 'to' => 'https://evil.example']))->assertRedirect('/admin');
        $this->get(route('demo.login', ['email' => UserSeeder::email(), 'to' => '//evil.example']))->assertRedirect('/admin');
    }
}
