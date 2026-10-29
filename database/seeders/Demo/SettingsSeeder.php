<?php

namespace Database\Seeders\Demo;

use Aurora\Enums\ApiScope;
use Aurora\Models\ApiKey;
use Aurora\Models\Page;
use Aurora\Models\Setting;
use Aurora\Models\Webhook;
use Aurora\Support\Copilot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put([
            'site_name' => 'Aurora',
            'tagline' => 'The CMS your editors enjoy and your developers trust. Built on Laravel and Filament.',
            'brand_color' => '#7c3aed',
            'accent_color' => '#22d3ee',
            'header_cta_label' => 'Get started',
            'header_cta_page' => Page::query()->whereSlug('installation')->value('id'),
            'footer_note' => 'Built with Aurora, naturally.',
            'contact_email' => 'hello@aurora.test',
            'socials' => [
                ['network' => 'LinkedIn', 'url' => 'https://www.linkedin.com'],
                ['network' => 'GitHub', 'url' => 'https://github.com/shipaurora'],
            ],
            // The AI assistant starts switched off (no provider or key), with good instructions ready.
            'copilot' => ['instructions' => Copilot::DEFAULT_INSTRUCTIONS],
            // The REST API on, behind keys, so the APIs and their keys have something to show.
            'rest_api' => ['enabled' => true, 'requires_key' => true],
            'announcement' => [
                'enabled' => true,
                'text' => 'Aurora 1.4 is out!',
                'link_label' => 'What\'s new',
                'url' => '/release-notes',
            ],
        ]);

        $admin = PageSeeder::author()?->getKey();

        // Keys for the APIs, named after what uses them (the keys themselves are never shown again).
        foreach ([
            ['Mobile app', [ApiScope::PagesList, ApiScope::PagesRead, ApiScope::MediaRead, ApiScope::MenusRead], 2],
            ['Website search widget', [ApiScope::Search], 30],
        ] as [$name, $scopes, $hoursAgo]) {
            // Seeding again keeps a key that's there: whatever uses it keeps working.
            if (ApiKey::query()->where('name', $name)->exists()) {
                continue;
            }

            $key = 'aurora_'.Str::random(40);

            ApiKey::query()->create([
                'name' => $name,
                'key_hash' => ApiKey::hash($key),
                'hint' => Str::limit($key, 11, ''),
                'scopes' => array_map(fn (ApiScope $scope): string => $scope->value, $scopes),
                'created_by_id' => $admin,
                'last_used_at' => now()->subHours($hoursAgo),
            ]);
        }

        // Told when pages go live, so the mobile app can refresh.
        Webhook::query()->updateOrCreate(['url' => 'https://hooks.example.com/aurora/rebuild-app'], [
            'events' => ['page.published', 'page.unpublished'],
            'is_active' => true,
            'last_status' => 200,
            'last_sent_at' => now()->subHours(5),
        ]);
    }
}
