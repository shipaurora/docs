<?php

namespace Database\Seeders\Demo;

use Aurora\Enums\AiChannel;
use Aurora\Enums\PageStatus;
use Aurora\Enums\RevisionSource;
use Aurora\Models\Asset;
use Aurora\Models\AssetTag;
use Aurora\Models\Form;
use Aurora\Models\Layout;
use Aurora\Models\McpActivity;
use Aurora\Models\Page;
use Aurora\Models\Redirect;
use Aurora\Models\User;
use Aurora\Support\AssetLibrary;
use Aurora\Support\BlockTranslations;
use Aurora\Support\BlockTreeBuilder;
use Aurora\Support\ContentLocale;
use Aurora\Support\RevisionContext;
use Aurora\Support\WorkingCopies;
use Database\Seeders\Demo\Pages\DocsPages;
use Database\Seeders\Demo\Pages\GuidePages;
use Database\Seeders\Demo\Pages\ProductPages;
use Database\Seeders\Demo\Support\B;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Aurora's own website, which is also its public documentation: the product pages, the docs
 * (the README, page by page, in the docs layout), the guides and the rest. The contact page
 * has changes waiting, so the publishing workflow has something to show.
 */
class PageSeeder extends Seeder
{
    use DocsPages, GuidePages, ProductPages;

    /**
     * Page ids by slug, so blocks can link to pages (links follow a page when its slug changes).
     *
     * @var array<string, string>
     */
    protected array $ids = [];

    /**
     * Media library asset ids by demo path ("demo/fjord.jpg"), so seeded content uses assets.
     *
     * @var array<string, string>
     */
    protected array $assets = [];

    protected ?Form $contactForm = null;

    /**
     * Titles, alt text and folders for the demo images (an empty folder means the top level).
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    protected const IMAGES = [
        'fjord' => ['Preikestolen over Lysefjorden', 'A steep cliff above a deep blue fjord under a clear sky', 'Photos/Norway'],
        'mountains' => ['Green ridge in the mist', 'Grassy mountain ridges disappearing into low clouds', 'Photos/Norway'],
        'forest' => ['Valley with granite cliffs', 'Pine trees in a valley beneath tall granite cliffs', 'Photos/Nature'],
        'lake' => ['River through the forest', 'A person standing by a rushing river in a misty forest', 'Photos/Nature'],
        'camp' => ['Winter base camp', 'Yellow tents in deep snow below snowy peaks', ''],
        'city' => ['City at dusk', 'A city skyline at sunset seen from above', ''],
        'desk' => ['Laptop and notebook', 'A laptop beside an open notebook with sketches on a wooden desk', 'Studio'],
        'workspace' => ['Designer\'s desk', 'Top-down view of a desk with a laptop, sketches, a phone and books', 'Studio'],
        'coffee' => ['Coffee mug', 'A red patterned coffee mug on a table', ''],
        'screen-dashboard' => ['Screenshot: dashboard', 'The Aurora dashboard: a greeting with quick actions, content statistics, recent changes and pages that need attention', ''],
        'screen-command-palette' => ['Screenshot: command palette', 'The command palette open over the admin, listing actions, screens and pages to jump to', ''],
        'screen-pages' => ['Screenshot: pages', 'The pages list in the Aurora admin, with status tabs, each page\'s address, status, layout and last edit', ''],
        'screen-page-seo' => ['Screenshot: search engine settings', 'A page\'s search engine settings: SEO checks, a Google result preview, and the SEO title and description', ''],
        'screen-unpublished' => ['Screenshot: unpublished changes', 'A live page with a banner saying its saved changes are not on the website yet, with Publish changes, Preview and Discard', ''],
        'screen-history' => ['Screenshot: page history', 'A page\'s history: every revision with its source, the person and a summary, ready to preview or restore', ''],
        'screen-editor' => ['Screenshot: visual editor', 'The Aurora visual editor: the block palette on the left, the page on the canvas and the selected block\'s fields on the right', ''],
        'screen-editor-mobile' => ['Screenshot: visual editor at mobile width', 'The visual editor showing the page at mobile width, with the structure outline open', ''],
        'screen-media' => ['Screenshot: media library', 'The Aurora media library: folders, breadcrumbs and image cards with coloured tags', ''],
        'screen-media-file' => ['Screenshot: a file in the media library', 'An image\'s page in the media library: the preview with its focal point, alt text, caption, folder and tags', ''],
        'screen-forms' => ['Screenshot: form builder', 'The form builder: fields with labels and widths, and the form\'s settings', ''],
        'screen-submissions' => ['Screenshot: form submissions', 'A form\'s submissions with their status, ready to read, reply to or export', ''],
        'screen-menu' => ['Screenshot: menu editor', 'The menu editor, with nested menu items linked to pages', ''],
        'screen-redirects' => ['Screenshot: redirects', 'The redirects list, with old addresses, where they go, their type and how often they are used', ''],
        'screen-users' => ['Screenshot: users', 'The users list with each person\'s roles, sites and how they sign in', ''],
        'screen-roles' => ['Screenshot: role permissions', 'A role\'s permissions in plain language, grouped by pages, media, forms, menus and more', ''],
        'screen-activity' => ['Screenshot: activity log', 'The activity log: who changed what, when, and which fields changed', ''],
        'screen-translations' => ['Screenshot: translations', 'The translations screen: every label, one tab per language, with a count of what is missing', ''],
        'screen-languages' => ['Screenshot: languages', 'Settings for languages: the default language, address prefixes and translation progress', ''],
        'screen-settings' => ['Screenshot: site settings', 'Site settings in Aurora: the site name, contact address, tagline and brand colours with a live preview', ''],
        'screen-security' => ['Screenshot: security settings', 'Security settings: switches to require two-factor sign-in and passkeys for everyone', ''],
        'screen-page-cache' => ['Screenshot: page cache', 'Performance settings: the page cache switch, how long pages are kept, and a button to clear it', ''],
        'screen-api' => ['Screenshot: REST API settings', 'The REST API settings: switched on, whether a key is needed, and how to call it', ''],
        'screen-api-keys' => ['Screenshot: API keys', 'API keys, each named after what uses it, with its scopes and when it was last used', ''],
        'screen-webhooks' => ['Screenshot: webhooks', 'Webhook settings: addresses told about content events, with a signing secret', ''],
        'screen-connect-ai' => ['Screenshot: connect AI apps', 'Connect AI apps: step-by-step setup for Claude and other apps, with copy buttons', ''],
        'screen-themes' => ['Screenshot: admin themes', 'Appearance settings: the admin themes to choose from, each with a picture', ''],
        'screen-search' => ['Screenshot: search settings', 'Search and SEO settings: a search to try against the index, with the pages it finds', ''],
        'screen-import' => ['Screenshot: WordPress import', 'System settings: importing a WordPress export', ''],
    ];

    /**
     * Demo tags: name => [colour, image names].
     *
     * @var array<string, array{0: string, 1: array<int, string>}>
     */
    protected const TAGS = [
        'Hero' => ['#8b5cf6', ['fjord', 'mountains', 'camp', 'city']],
        'Landscape' => ['#22c55e', ['fjord', 'mountains', 'forest', 'lake', 'camp']],
        'Winter' => ['#0ea5e9', ['camp']],
        'Office' => ['#f59e0b', ['desk', 'workspace', 'coffee']],
        'Portrait' => ['#ec4899', ['thomas']],
        'Licensed' => ['#64748b', ['fjord', 'city']],
        'Screenshot' => ['#7c3aed', ['screen-dashboard', 'screen-pages', 'screen-editor', 'screen-media', 'screen-menu', 'screen-settings', 'screen-security']],
    ];

    /**
     * The site's other pages under a parent (the docs are arranged by DOCS).
     *
     * @var array<string, array<int, string>>
     */
    protected const PARENTS = [
        'guides' => ['build-a-landing-page', 'let-claude-draft-a-page', 'add-your-own-block', 'run-several-sites', 'move-from-statamic'],
        'legal' => ['privacy', 'terms'],
    ];

    public function run(): void
    {
        $this->createAssets();

        // Every page, revision and change is by the site's first admin: the account made with
        // aurora:install or make:aurora-user. No account is seeded (its password would be in
        // this public code); without one, the content is simply nobody's.
        $admin = static::author();

        // This website's own layouts, beside Aurora's: views in this app (resources/views/layouts).
        // The homepage's header floats over its first block (its own take on Aurora's landing
        // page layout); the docs have the docs menu beside the page.
        Layout::query()->updateOrCreate(['handle' => 'landing'], [
            'name' => 'Landing page',
            'description' => 'Transparent header floating over the first block.',
            'icon' => 'phosphor-sparkle',
            'view' => 'layouts.landing',
        ]);
        Layout::query()->updateOrCreate(['handle' => 'docs'], [
            'name' => 'Docs',
            'description' => 'The docs menu beside the page, for documentation.',
            'icon' => 'phosphor-book-open-text',
            'view' => 'layouts.docs',
            'sort' => 5,
        ]);

        $pages = [
            ['home', 'Home', 'landing', PageStatus::Published, $admin, 'Aurora is a content management system for Laravel, built on Filament.', true],
            ['why-aurora', 'Why Aurora', 'standard', PageStatus::Published, $admin, 'The CMS is a dependency, the website is the app: the thinking behind Aurora, and how it differs from Statamic.'],
            ['features', 'Features', 'standard', PageStatus::Published, $admin, 'A tour of everything in the admin, from the visual editor to the APIs.'],
            ['docs', 'Docs', 'docs', PageStatus::Published, $admin, 'Install Aurora, build a website on it and run it with your team.'],
            ...$this->docsPages($admin),
            ['guides', 'Guides', 'standard', PageStatus::Published, $admin, 'Short, practical how-tos for editors and developers.'],
            ['build-a-landing-page', 'Build a landing page in the visual editor', 'standard', PageStatus::Published, $admin, 'From an empty canvas to a published campaign page, step by step.'],
            ['let-claude-draft-a-page', 'Let Claude draft a page for you', 'standard', PageStatus::Published, $admin, 'Connect Claude over MCP and ask it for a first draft you can refine.'],
            ['add-your-own-block', 'Add your own block', 'standard', PageStatus::Published, $admin, 'Make a block with one command: a PHP class, a Blade view and the helpers that render it.'],
            ['run-several-sites', 'Run several websites from one admin', 'standard', PageStatus::Published, $admin, 'Domains, languages and people for more than one website.'],
            ['move-from-statamic', 'Move a Statamic site to Aurora', 'standard', PageStatus::Published, $admin, 'Port a Statamic website in its own Laravel project: Aurora beside Statamic, the content brought across, then the switch.'],
            ['release-notes', 'Release notes', 'standard', PageStatus::Published, $admin, 'What changed in each version of Aurora.'],
            ['components', 'Component library', 'standard', PageStatus::Published, $admin, 'Every block this website is built with, on one page.'],
            ['contact', 'Contact', 'standard', PageStatus::Published, $admin, 'Questions about Aurora, or a demo for your team.'],
            ['legal', 'Legal', 'standard', PageStatus::Published, $admin, 'The terms and policies for this website.'],
            ['privacy', 'Privacy policy', 'standard', PageStatus::Published, $admin, 'How we handle your data.'],
            ['terms', 'Terms of service', 'standard', PageStatus::Published, $admin, 'The terms that apply to this website.'],
        ];

        // Create every page first so blocks can link to them by id.
        foreach ($pages as $index => [$slug, $title, $layout, $status, $owner, $excerpt]) {
            $page = RevisionContext::as(RevisionSource::System, 'Created the page', fn () => Page::query()->create([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'layout_id' => Layout::query()->where('handle', $layout)->value('id'),
                'status' => $status,
                'published_at' => $status === PageStatus::Published ? now()->subDays(count($pages) - $index) : null,
                'is_homepage' => $pages[$index][6] ?? false,
                'author_id' => $owner?->id,
                'last_edited_by_id' => $owner?->id,
                'og_image' => $this->asset(match ($slug) {
                    'home', 'features' => 'demo/screen-editor.jpg',
                    'build-a-landing-page' => 'demo/screen-editor.jpg',
                    'let-claude-draft-a-page' => 'demo/screen-connect-ai.jpg',
                    'add-your-own-block' => 'demo/workspace.jpg',
                    'run-several-sites' => 'demo/screen-languages.jpg',
                    'move-from-statamic' => 'demo/workspace.jpg',
                    default => null,
                }),
            ]));

            $this->ids[$slug] = $page->id;
        }

        // Where each page sits (/docs/get-started/installation, /legal/privacy): set once every
        // page exists, so a parent may come later in the list than its subpages.
        foreach ([...$this->docsParents(), ...self::PARENTS] as $parent => $children) {
            Page::query()->whereKey(array_map(fn (string $slug): string => $this->ids[$slug], $children))->toBase()->update(['parent_id' => $this->ids[$parent]]);
        }

        $this->contactForm = $this->createContactForm();

        foreach ($this->ids as $slug => $id) {
            $page = Page::query()->find($id);

            $this->actAs($page->author ?? $admin);

            RevisionContext::as(RevisionSource::Canvas, 'Designed the page', fn () => $page->update([
                'blocks' => BlockTreeBuilder::flatten($this->withAssets($this->blocksFor($slug))),
            ]));
        }

        auth()->logout();

        $this->translate();

        $this->tellTheStory($admin);

        // Sorted by last edit, the pages list shows them in the order above, so Home leads.
        foreach (array_values($this->ids) as $position => $id) {
            Page::query()->whereKey($id)->toBase()->update(['updated_at' => now()->subMinutes($position)]);
        }
    }

    /**
     * A page's blocks: its own method (blocksForWhyAurora()), a docs section's overview, or
     * a legal page.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function blocksFor(string $slug): array
    {
        $method = 'blocksFor'.str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)));

        return match (true) {
            method_exists($this, $method) => $this->{$method}(),
            array_key_exists($slug, self::DOCS) => $this->docsSection($slug),
            default => $this->legalPage($slug),
        };
    }

    /**
     * A little history, so the revision list, the AI activity and the "Changes waiting" tab
     * have something to show.
     */
    protected function tellTheStory(?User $admin): void
    {
        $home = Page::query()->find($this->ids['home']);
        $home->revisions()->create([
            'user_id' => $admin?->id,
            'source' => RevisionSource::Mcp,
            'summary' => 'Rewrote the hero headline and added the developer section',
            'snapshot' => $home->toSnapshot(),
            'created_at' => now()->subHours(3),
        ]);

        // Old addresses kept working: from the previous site, a printed brochure, and a page renamed.
        foreach ([
            ['/getting-started', ['page_id' => $this->ids['installation']], 301, 42],
            ['/docs/getting-started', ['page_id' => $this->ids['installation']], 301, 17],
            ['/brochure.pdf', ['asset_id' => Asset::query()->where('title', 'Brand guidelines')->value('id')], 301, 6],
            ['/github', ['to_url' => 'https://github.com/shipaurora'], 302, 128],
        ] as [$from, $to, $status, $hits]) {
            Redirect::query()->create(['from_path' => $from, 'status_code' => $status, ...$to])
                ->forceFill(['hits' => $hits, 'last_hit_at' => now()->subHours($hits % 24 + 1)])->save();
        }

        // What Claude did over MCP when it rewrote the homepage's hero, in the AI activity log.
        foreach ([
            ['list_block_types', 'Listed the block types', null, 182],
            ['get_page', 'Read the page "Home"', $home, 181],
            ['update_block', 'Changed the hero on "Home"', $home, 180],
            ['add_block', 'Added a code sample to "Home"', $home, 179],
            ['seo_report', 'Checked the SEO of 31 pages', null, 60],
        ] as [$tool, $summary, $subject, $minutesAgo]) {
            // Seeding again replaces these samples (they point at the pages made this time) and
            // leaves real activity alone.
            McpActivity::query()->where('tool', $tool)->where('summary', $summary)->delete();

            McpActivity::query()->create([
                'user_id' => $admin?->id,
                'channel' => AiChannel::Mcp,
                'tool' => $tool,
                'summary' => $summary,
                'succeeded' => true,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'arguments' => $subject ? ['page' => $subject->getKey()] : [],
            ])->forceFill(['created_at' => now()->subMinutes($minutesAgo)])->save();
        }

        // A new line on the contact page, saved but not published yet: changes waiting on a live page.
        $contact = Page::query()->find($this->ids['contact']);
        $this->actAs($admin);

        RevisionContext::as(RevisionSource::Canvas, 'Added office hours to the contact page', fn () => WorkingCopies::capture(fn () => $contact->update([
            'excerpt' => 'Questions about Aurora, or a demo for your team. We answer on weekdays, 9–16 CET.',
            'blocks' => BlockTreeBuilder::flatten($this->blocksForContact(officeHours: true)),
        ])));

        auth()->logout();
    }

    /**
     * Every language with a dictionary in seeders/translations ("nb.json": English text =>
     * that language's) gets the pages', images' and form's text. Names, e-mail addresses and
     * anything else not in it stay as they are.
     */
    protected function translate(): void
    {
        foreach (ContentLocale::translations() as $locale => $language) {
            $file = __DIR__."/translations/{$locale}.json";

            if (! is_file($file)) {
                continue;
            }

            $dictionary = json_decode(file_get_contents($file), true);

            foreach (Page::query()->get() as $page) {
                $this->translatePage($page, $locale, $language, $dictionary);
            }

            $this->translateForm($this->contactForm, $locale, $dictionary);

            foreach (Asset::query()->get() as $asset) {
                foreach (['alt', 'caption'] as $attribute) {
                    $text = $asset->getTranslation($attribute, ContentLocale::default(), false);

                    if (isset($dictionary[$text])) {
                        $asset->setTranslation($attribute, $locale, $dictionary[$text]);
                    }
                }

                $asset->save();
            }
        }
    }

    /**
     * The form's text in another language; its fields stay the same.
     *
     * @param  array<string, string>  $dictionary
     */
    protected function translateForm(Form $form, string $locale, array $dictionary): void
    {
        $fields = [];

        foreach ($form->fields as $field) {
            foreach (Form::FIELD_TEXT as $attribute) {
                $original = $field['data'][$attribute] ?? null;
                $translated = $this->translated($original, $locale, $dictionary);

                if (filled($original) && $translated !== $original) {
                    $fields[$field['data']['name']][$attribute] = $translated;
                }
            }
        }

        foreach (['submit_label', 'success_message'] as $attribute) {
            $text = $form->getTranslation($attribute, ContentLocale::default(), false);

            if (isset($dictionary[$text])) {
                $form->setTranslation($attribute, $locale, $dictionary[$text]);
            }
        }

        $form->field_translations = [...$form->field_translations ?? [], $locale => $fields];
        $form->save();
    }

    /**
     * @param  array<string, string>  $dictionary
     */
    protected function translatePage(Page $page, string $locale, string $language, array $dictionary): void
    {
        $default = ContentLocale::default();
        $blocks = [];

        foreach ($page->blocks ?? [] as $block) {
            foreach (BlockTranslations::fieldsOf($block['type']) as $field) {
                $original = $block['data'][$field] ?? null;
                $translated = $this->translated($original, $locale, $dictionary);

                if (filled($original) && $translated !== $original) {
                    $blocks[$block['id']][$field] = $translated;
                }
            }
        }

        foreach (['title', 'excerpt', 'meta_title', 'meta_description'] as $attribute) {
            $text = $page->getTranslation($attribute, $default, false);

            if (isset($dictionary[$text])) {
                $page->setTranslation($attribute, $locale, $dictionary[$text]);
            }
        }

        $page->block_translations = [...$page->block_translations ?? [], $locale => $blocks];

        $this->actAs($page->author);

        RevisionContext::as(RevisionSource::Canvas, "Translated into {$language}", fn () => $page->save());

        auth()->logout();
    }

    /**
     * A text field's value in another language: its strings looked up, lists item by item.
     *
     * @param  array<string, string>  $dictionary
     */
    protected function translated(mixed $value, string $locale, array $dictionary): mixed
    {
        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->translated($item, $locale, $dictionary), $value);
        }

        if (! is_string($value)) {
            return $value;
        }

        // The legal pages' date is today's: written out in the language.
        if (preg_match('/^Last updated (.+)\.$/', $value, $match)) {
            return __('Last updated :date.', ['date' => Carbon::parse($match[1])->locale($locale)->isoFormat('LL')], $locale);
        }

        return $dictionary[$value] ?? $value;
    }

    protected function id(string $slug): ?string
    {
        return $this->ids[$slug] ?? null;
    }

    protected function createContactForm(): Form
    {
        // Updated in place when seeding again: what visitors sent through it stays.
        $form = Form::query()->updateOrCreate(['name' => 'Contact'], [
            'submit_label' => 'Send message',
            'success_message' => 'Thanks! We reply within one working day.',
            'notify' => ['hello@aurora.test'],
            'fields' => [
                ['type' => 'text', 'data' => ['label' => 'Your name', 'name' => 'name', 'required' => true, 'width' => 'half']],
                ['type' => 'email', 'data' => ['label' => 'Email', 'name' => 'email', 'required' => true, 'width' => 'half']],
                ['type' => 'text', 'data' => ['label' => 'Company', 'name' => 'company', 'width' => 'half']],
                ['type' => 'select', 'data' => ['label' => 'Team size', 'name' => 'team_size', 'options' => ['Just me', '2–10', '11–50', 'More than 50'], 'width' => 'half']],
                ['type' => 'checkboxes', 'data' => ['label' => 'What are you interested in?', 'name' => 'interests', 'options' => ['The visual editor', 'AI and MCP', 'Several sites', 'The REST and GraphQL APIs', 'Moving from another CMS']]],
                ['type' => 'textarea', 'data' => ['label' => 'How can we help?', 'name' => 'message', 'required' => true, 'placeholder' => 'What are you building, and what do you need?']],
                ['type' => 'consent', 'data' => ['label' => 'Consent', 'name' => 'consent', 'required' => true, 'text' => 'I agree that the Aurora team stores my details to answer this request.']],
            ],
        ]);

        return $form;
    }

    protected function createAssets(): void
    {
        foreach (glob(__DIR__.'/images/*.jpg') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            [$title, $alt, $folder] = self::IMAGES[$name] ?? [null, null, null];

            $this->assets['demo/'.basename($file)] = AssetLibrary::fromFile($file, array_filter([
                'title' => $title,
                'alt' => $alt,
                'folder' => $folder,
            ]))->id;
        }

        // The demo's one person, for the component library's team member and testimonial.
        $this->assets['demo/thomas.jpg'] = AssetLibrary::fromFile(__DIR__.'/images/avatars/thomas.jpg', [
            'title' => 'Portrait: Thomas Alrek',
            'alt' => 'Portrait of Thomas Alrek',
            'folder' => 'People',
        ])->id;

        // Not everything is an image: a PDF, a spreadsheet and a sound, to show the library's other files.
        foreach (['brand-guidelines.pdf' => 'Brand guidelines', 'price-list.csv' => 'Price list', 'studio-jingle.wav' => 'Studio jingle'] as $file => $title) {
            AssetLibrary::fromFile(__DIR__.'/files/'.$file, ['title' => $title, 'folder' => 'Documents']);
        }

        foreach (self::TAGS as $name => [$color, $images]) {
            AssetTag::query()
                ->create(['name' => $name, 'color' => $color])
                ->assets()
                ->attach(array_map(fn (string $image): string => $this->assets['demo/'.$image.'.jpg'], $images));
        }
    }

    /**
     * The account the site's content is credited to: its first Super Admin, or nobody.
     */
    public static function author(): ?User
    {
        return User::query()->role(User::SUPER_ADMIN)->oldest()->first();
    }

    /**
     * Edits made as this person (their revisions and unpublished changes), or as nobody.
     */
    protected function actAs(?User $user): void
    {
        $user !== null ? auth()->setUser($user) : auth()->forgetUser();
    }

    protected function asset(?string $path): ?string
    {
        return $path === null ? null : ($this->assets[$path] ?? $path);
    }

    /**
     * Swap every "demo/…" image path in a block tree for its media library asset id.
     */
    protected function withAssets(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->assets[$value] ?? $value;
        }

        return is_array($value) ? array_map(fn (mixed $child): mixed => $this->withAssets($child), $value) : $value;
    }

    /**
     * A screenshot of the admin, framed: "dashboard" is demo/screen-dashboard.jpg.
     *
     * @return array<string, mixed>
     */
    protected function screenshot(string $name, ?string $caption = null): array
    {
        return B::image("demo/screen-{$name}.jpg", '', $caption, 'auto', true);
    }

    /* ── Legal ────────────────────────────────────────────────────────── */

    protected function blocksForLegal(): array
    {
        return [
            B::band([
                B::heading('Legal', null, 'The terms and policies that apply to this website.', 'xl', 'left', 'h1'),
                B::spacer(),
                B::grid([
                    B::card('Privacy policy', 'How we handle your data.', null, null, $this->id('privacy'), 'bordered', 'Read'),
                    B::card('Terms of service', 'The terms that apply to this website.', null, null, $this->id('terms'), 'bordered', 'Read'),
                ], 2),
            ], 'white', 'lg', 'narrow'),
        ];
    }

    protected function legalPage(string $slug): array
    {
        $title = $slug === 'privacy' ? 'Privacy policy' : 'Terms of service';

        return [
            B::band([
                B::heading($title, 'Legal', __('Last updated :date.', ['date' => now()->format('F j, Y')], 'en'), 'lg', 'left', 'h1'),
                B::spacer('sm'),
                B::rich('<p>This is demo content. On a real site this page would hold the full legal text, kept by your legal team in the same editor as everything else.</p><h2>1. Who we are</h2><p>The Aurora team, the makers of Aurora CMS.</p><h2>2. What we collect</h2><p>Only what we need to answer you, and never more.</p><h2>3. Your rights</h2><p>You can ask us for a copy of your data, or to delete it, at any time.</p>'),
            ], 'white', 'lg', 'narrow'),
        ];
    }
}
