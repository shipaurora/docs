<?php

namespace Tests;

use Aurora\Database\Seeders\RolesAndPermissionsSeeder;
use Aurora\Models\Site;
use Aurora\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * Tests work in the admin on the default site, as someone who just signed in does. They
     * never reach the internet: a request no test faked fails instead of slowing the run.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        if (in_array(RefreshDatabase::class, class_uses_recursive($this), true)) {
            Filament::setCurrentPanel('admin');
            Filament::setTenant(Site::default(), isQuiet: true);
        }
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        return tap(User::factory()->create($attributes))->assignRole($role);
    }
}
