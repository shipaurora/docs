<?php

namespace Database\Seeders\Demo\Pages;

use Aurora\Enums\PageStatus;
use Aurora\Models\User;
use Database\Seeders\Demo\Support\B;

/**
 * The docs: the README, page by page, under /docs in the docs layout. Sections hold the
 * pages; the docs menu (MenuSeeder) lists them in the same order.
 */
trait DocsPages
{
    /**
     * The docs in reading order: section slug => [title, summary, [page slug => [title, summary]]].
     *
     * @var array<string, array{0: string, 1: string, 2: array<string, array{0: string, 1: string}>}>
     */
    public const DOCS = [
        'get-started' => ['Get started', 'Install Aurora, set it up and see how it fits together.', [
            'installation' => ['Installation', 'Requirements, a new project from the template, and adding Aurora to an existing app.'],
            'quick-setup' => ['Quick setup', 'Eight steps from a fresh install to a website your team can work on.'],
            'architecture' => ['Philosophy and architecture', 'Why the CMS is a dependency and the website is the app, and what Aurora is built on.'],
        ]],
        'using-the-admin' => ['Using the admin', 'What editors do every day, screen by screen.', [
            'finding-your-way' => ['Finding your way', 'The sidebar, the dashboard, the command palette, search and notifications.'],
            'pages' => ['Pages', 'The pages list, the page form, previews, edit locks and history.'],
            'publishing' => ['Publishing workflow', 'Drafts, review, scheduling, unpublished changes and revisions.'],
            'visual-editor' => ['The visual editor', 'Compose pages from blocks on the real design, and translate them.'],
            'media-library' => ['Media library', 'Folders, tags, focal points, image formats and access.'],
            'forms' => ['Forms', 'Forms with conditional fields, and what to do with the answers.'],
            'menus-redirects-trash' => ['Menus, redirects and trash', 'Navigation that follows your pages, redirects, and getting deleted things back.'],
        ]],
        'building-a-site' => ['Building a site', 'For developers: blocks, layouts, settings and search.', [
            'project-structure' => ['Structuring a project', 'Where your blocks, layouts, CSS and set-up live.'],
            'creating-blocks' => ['Creating blocks', 'A PHP class and a Blade view: what a block is and how to make one.'],
            'block-views' => ['Block views and helpers', 'Render text, links, images and slots with helpers or Blade directives.'],
            'canvas-styles' => ['The canvas shows your CSS', 'How the visual editor shows blocks in the website\'s own stylesheet.'],
            'layouts' => ['Layouts and the website', 'The views that draw whole pages, and everything Aurora serves.'],
            'your-own-settings' => ['Your own settings', 'Add tabs to Settings and read them in your templates.'],
            'website-screens' => ['Your own screens and models', 'Admin screens, models published like pages, and menu panels for what the website adds.'],
            'site-search' => ['Search', 'Search live pages with Laravel Scout, per site and language.'],
        ]],
        'running-a-site' => ['Running a site', 'People, languages, sites, settings and speed.', [
            'users-and-roles' => ['Users, roles and permissions', 'Invitations, sign-in with passkeys, roles in plain language and the activity log.'],
            'languages' => ['Languages and translations', 'Content in every language, and every label in the admin.'],
            'several-websites' => ['Several websites', 'Run more than one website, each with its own domain, from one admin.'],
            'settings' => ['Settings', 'Every page and tab in Settings, and what each one holds.'],
            'seo' => ['SEO checks', 'What the audit looks for on every page, and how much each check matters.'],
            'performance-and-access' => ['Page cache and access', 'Serve pages ready-made, and decide who may see them.'],
            'admin-themes' => ['Admin themes', 'Choose the admin\'s look, or write a theme of your own.'],
            'moving-from-wordpress' => ['Moving from WordPress', 'Bring pages, posts, images and menus over from a WordPress export.'],
            'moving-from-another-cms' => ['Moving from another CMS', 'Import pages, translations, files, redirects and menus from code.'],
        ]],
        'ai-and-integrations' => ['AI and integrations', 'AI assistants, the REST and GraphQL APIs, and webhooks.', [
            'ai' => ['AI: MCP, the assistant and Write with AI', 'Connect Claude and other apps, chat with the assistant, and let AI write and translate.'],
            'headless-apis' => ['REST and GraphQL APIs', 'Read pages, menus, media and settings from apps, with API keys and scopes.'],
            'webhooks' => ['Webhooks and events', 'Tell other systems when content changes, or listen in your own app.'],
        ]],
        'reference' => ['Reference', 'Configuration, helpers, routes, data, commands and packages.', [
            'configuration' => ['Configuration', 'Every config key, environment variable and publish tag, and where everything else is set.'],
            'blade-helpers' => ['Blade helpers', 'Every helper, directive and component for block views and layouts.'],
            'routing' => ['Routing', 'How an address finds its page, and where the website\'s own routes fit in.'],
            'data-reference' => ['Data reference', 'The tables and columns behind pages, layouts, media, redirects and menus.'],
            'artisan-commands' => ['Artisan commands', 'Install, make blocks, import and keep translations in sync.'],
            'packages-and-development' => ['Packages and development', 'What each package holds, and how to work on Aurora itself.'],
        ]],
    ];

    /**
     * The docs' pages for PageSeeder: each section, then its pages.
     *
     * @return array<int, array{0: string, 1: string, 2: string, 3: PageStatus, 4: ?User, 5: string}>
     */
    protected function docsPages(?User $admin): array
    {
        $pages = [];

        foreach (self::DOCS as $section => [$title, $summary, $docs]) {
            $pages[] = [$section, $title, 'docs', PageStatus::Published, $admin, $summary];

            foreach ($docs as $slug => [$docTitle, $docSummary]) {
                $pages[] = [$slug, $docTitle, 'docs', PageStatus::Published, $admin, $docSummary];
            }
        }

        return $pages;
    }

    /**
     * Where the docs sit: the sections under Docs, each page under its section.
     *
     * @return array<string, array<int, string>>
     */
    protected function docsParents(): array
    {
        return [
            'docs' => array_keys(self::DOCS),
            ...array_map(fn (array $section): array => array_keys($section[2]), self::DOCS),
        ];
    }

    protected function blocksForDocs(): array
    {
        return [
            B::band([
                B::heading('Documentation', 'Docs', 'Everything you need to install Aurora, build a website on it and run it with your team. Start at the top, or jump to what you need.', 'xl', 'left', 'h1'),
            ], 'white', 'md', 'narrow'),
            ...array_map(fn (string $section): array => B::band([
                B::heading(self::DOCS[$section][0], null, self::DOCS[$section][1], 'md'),
                B::spacer('sm'),
                $this->docCards($section),
            ], 'white', 'sm', 'narrow'), array_keys(self::DOCS)),
            B::band([
                ['type' => 'alert', 'data' => ['variant' => 'brand', 'title' => 'Prefer to learn by doing?', 'body' => 'The guides walk through real tasks step by step: build a landing page, let Claude draft one, add your own block.']],
                B::buttons([B::button('Browse the guides', $this->id('guides'), 'secondary', icon: 'arrow-right')]),
            ], 'white', 'md', 'narrow'),
        ];
    }

    /**
     * A section's overview: what it covers, and a card per page.
     */
    protected function docsSection(string $section): array
    {
        return [
            B::band([
                B::heading(self::DOCS[$section][0], 'Docs', self::DOCS[$section][1], 'xl', 'left', 'h1'),
            ], 'white', 'md', 'narrow'),
            B::band([$this->docCards($section)], 'white', 'sm', 'narrow'),
        ];
    }

    protected function docCards(string $section): array
    {
        return B::grid(array_map(
            fn (string $slug, array $doc): array => B::card($doc[0], $doc[1], null, null, $this->id($slug), 'bordered', 'Read'),
            array_keys(self::DOCS[$section][2]),
            self::DOCS[$section][2],
        ), 2);
    }

    /**
     * A docs page: its title and summary, the content, and the pages before and after it.
     *
     * @param  array<int, array<string, mixed>>  $content
     */
    protected function doc(string $slug, array $content): array
    {
        $order = [];

        foreach (self::DOCS as $section => [$sectionTitle, , $docs]) {
            foreach ($docs as $docSlug => [$title, $summary]) {
                $order[$docSlug] = [$title, $summary, $sectionTitle];
            }
        }

        $slugs = array_keys($order);
        $position = array_search($slug, $slugs, true);
        $previous = $slugs[$position - 1] ?? null;
        $next = $slugs[$position + 1] ?? null;
        [$title, $summary, $sectionTitle] = $order[$slug];

        $neighbour = fn (?string $other, string $badge): array => $other === null
            ? B::card('The guides', 'Put it into practice, step by step.', null, $badge, $this->id('guides'), 'bordered', 'Browse the guides')
            : B::card($order[$other][0], $order[$other][1], null, $badge, $this->id($other), 'bordered', 'Read');

        return [
            B::band([
                B::heading($title, $sectionTitle, $summary, 'lg', 'left', 'h1'),
            ], 'white', 'sm', 'narrow'),
            B::band($content, 'white', 'none', 'narrow'),
            B::band([
                B::columns([
                    [$previous === null ? B::card('Docs', 'Back to the overview.', null, 'Previous', $this->id('docs'), 'bordered', 'Open') : $neighbour($previous, 'Previous')],
                    [$neighbour($next, 'Next')],
                ], '1-1', 'md', 'stretch'),
            ], 'white', 'md', 'narrow'),
        ];
    }

    protected function note(string $title, string $body, string $variant = 'info'): array
    {
        return ['type' => 'alert', 'data' => ['variant' => $variant, 'title' => $title, 'body' => $body]];
    }

    /**
     * Links to related docs, as a row of buttons.
     *
     * @param  array<string, string>  $links  slug => label
     */
    protected function seeAlso(array $links): array
    {
        return B::buttons(array_map(
            fn (string $slug, string $label): array => B::button($label, $this->id($slug), 'outline', size: 'sm', icon: 'arrow-right'),
            array_keys($links),
            $links,
        ));
    }

    /* ── Get started ──────────────────────────────────────────────────── */

    protected function blocksForInstallation(): array
    {
        return $this->doc('installation', [
            B::rich('<h2>Requirements</h2><ul><li>PHP 8.4 or newer, with the GD or Imagick extension</li><li>Composer 2 and Node.js 20+</li><li>A database: SQLite, MySQL/MariaDB or PostgreSQL. MySQL or MariaDB gives the best search, with full-text relevance.</li><li>A queue worker in production, for notifications, image formats and webhooks</li><li>HTTPS in production, which passkeys require</li></ul><h2>Start a new project</h2><p>New websites start from the <strong>application template</strong>. It\'s a complete Laravel application with the CMS already wired in, the page layouts published and ready to make your own, <code>app/Blocks</code> as the place for your own blocks, and the build set up for the website and the admin. The CMS itself comes in through Composer, so you update it with <code>composer update</code> while your website\'s code stays yours.</p>'),
            B::code(<<<'BASH'
composer create-project shipaurora/aurora my-site   # asks a few questions and sets it all up
cd my-site
BASH, 'bash'),
            B::rich('<p><code>aurora:install</code> walks you through setup:</p><ul><li>creates <code>.env</code> if needed and the application key;</li><li>runs the migrations, or with <code>--fresh</code> starts over;</li><li>publishes the admin\'s prebuilt theme and scripts, and the page layouts;</li><li>seeds what the CMS needs (roles, permissions, the site and its languages); a website\'s own content comes from its own seeders;</li><li>indexes pages for search, syncs labels from the lang files, creates the OAuth keys for AI apps and links public storage;</li><li>creates your admin account, a Super Admin;</li><li>runs the website\'s own seeders (<code>database/seeders/DatabaseSeeder</code>, its first content, such as the homepage) when you say yes, or with <code>--seed</code>.</li></ul><p>It can also run unattended:</p>'),
            B::code(<<<'BASH'
php artisan aurora:install \
    --site-name="Acme" --language=en --languages="nb:no,sv" \
    --name="Ada Admin" --email="$ADMIN_EMAIL" --password="$ADMIN_PASSWORD"
BASH, 'bash'),
            B::rich('<p>Serve it the way you usually do. With Laravel Herd it\'s <code>http://my-site.test</code>; otherwise use <code>php artisan serve</code>. Open <code>/admin</code> to sign in.</p>'),
            B::rich('<h2>Adding Aurora to an existing Laravel application</h2><p>Starting from the template is the recommended way. Aurora can also be added to an existing Laravel 13 application; you then set up yourself what the template sets up for you: the layouts and the site\'s CSS.</p>'),
            B::code("composer require shipaurora/cms\nphp artisan aurora:install", 'bash'),
            B::rich('<p>Aurora registers its own service providers and its Filament panel at <code>/admin</code>, and brings its default admin theme with it. Then add your own blocks. Optionally, <code>shipaurora/minimal</code> adds a second admin theme to choose from.</p>'),
            B::rich('<h2>Updating Aurora</h2><p>Update the CMS with Composer, run its migrations, and publish the admin\'s scripts again. Until the migrations have run, the site works as before, without the new features. The release notes say what each version changes.</p>'),
            B::code("composer update shipaurora/cms\nphp artisan migrate\nphp artisan filament:assets", 'bash'),
            $this->seeAlso(['quick-setup' => 'Quick setup', 'creating-blocks' => 'Creating blocks', 'release-notes' => 'Release notes']),
        ]);
    }

    protected function blocksForQuickSetup(): array
    {
        return $this->doc('quick-setup', [
            ['type' => 'steps', 'data' => ['layout' => 'vertical', 'items' => [
                ['title' => 'Sign in and brand the site', 'body' => 'Sign in at /admin and open Settings › Website › Branding: the site name, contact email, tagline, default share image and brand colours.'],
                ['title' => 'Languages', 'body' => 'Under Settings › Languages & sites, add the languages the website is written in, and their address prefixes.'],
                ['title' => 'Layouts', 'body' => 'The layouts are published to resources/views/vendor/aurora/layouts. Make them the site\'s own, and manage them under Settings › Templates › Layouts.'],
                ['title' => 'Menus', 'body' => 'Build the header and footer menus and place them in menu locations.'],
                ['title' => 'Pages', 'body' => 'Create the homepage and the rest in the visual editor.'],
                ['title' => 'People', 'body' => 'Invite colleagues under Users & Roles and give them roles.'],
                ['title' => 'AI (optional)', 'body' => 'Add an AI provider and key in Settings › AI Assistant for the assistant in the admin, and let people connect Claude and others under AI › Connect AI apps.'],
                ['title' => 'Production', 'body' => 'Run a queue worker (php artisan queue:work) and switch on Settings › Performance › Page cache.'],
            ]]],
            $this->screenshot('settings', 'Settings › Website › Branding: the site name, tagline and brand colours, with a live preview.'),
            $this->seeAlso(['settings' => 'All settings', 'layouts' => 'Layouts', 'users-and-roles' => 'Users and roles']),
        ]);
    }

    protected function blocksForArchitecture(): array
    {
        return $this->doc('architecture', [
            B::rich('<p>Aurora started from one question: <em>what if a client site\'s CMS were simply part of the Laravel application, rather than a platform the application lives inside?</em> Four principles follow from that.</p><ol><li><strong>The CMS is a dependency, the website is the app.</strong> Everything the CMS is comes in through Composer: models, admin, migrations, AI tools, admin styles. The application in <code>app/</code> and <code>resources/</code> is the website: its layouts, its components, its own blocks. Upgrading the CMS is <code>composer update</code>; it never touches the site, and the site never needs to fork the CMS.</li><li><strong>Edit the real thing.</strong> Editors shouldn\'t fill in forms and imagine the result. The canvas renders the same Blade components the website renders, with the site\'s own CSS.</li><li><strong>Content stays structured.</strong> Each block declares its fields in a Filament schema, so content is typed, validated, translatable, searchable and machine-readable. That is what makes the APIs and AI assistants dependable.</li><li><strong>Permissions are the boundary, everywhere.</strong> The admin, the visual editor, bulk actions, the MCP server and the built-in assistant all go through the same policies. Publishing is checked at the model level, so no surface can skip it.</li></ol><h2>The project</h2>'),
            B::code(<<<'TEXT'
your-site/                         ← your website: a Laravel app, started from the template
├── app/Providers/                 ← the site's set-up: its own settings tabs, block order
├── app/Blocks/                    ← the site's own blocks (found automatically)
├── resources/views/
│   ├── components/blocks/         ← views for the site's own blocks
│   ├── components/layouts/        ← the site's page shell (header, footer, menus)
│   └── vendor/aurora/layouts/     ← page layouts, published from Aurora and made the site's own
├── resources/css/
│   ├── app.css                    ← the website's CSS, also shown on the editor's canvas
│   └── site/tokens.css            ← the website's design tokens
└── vendor/aurora/                 ← the CMS, through Composer
    ├── cms/                       ← models, Filament admin, MCP, APIs, migrations, views, lang
    ├── borealis/                   ← the default admin theme ("Borealis")
    └── minimal/                   ← a second admin theme ("Minimal")
TEXT, 'text'),
            B::rich('<h2>Built on</h2><table><tr><th>Concern</th><th>Built on</th></tr><tr><td>Admin</td><td>Filament 5 (SPA mode), Livewire, Blade, Phosphor icons</td></tr><tr><td>Visual editor</td><td><code>carljanzell/filament-page-builder</code>, extended by Aurora</td></tr><tr><td>Media</td><td><code>spatie/laravel-medialibrary</code></td></tr><tr><td>Roles and permissions</td><td><code>spatie/laravel-permission</code></td></tr><tr><td>Translations</td><td><code>spatie/laravel-translatable</code>, <code>spatie/laravel-translation-loader</code></td></tr><tr><td>Menus</td><td><code>datlechin/filament-menu-builder</code></td></tr><tr><td>Search</td><td>Laravel Scout (a database engine with MySQL full-text, or any Scout driver)</td></tr><tr><td>AI</td><td><code>laravel/mcp</code>, <code>laravel/passport</code> (OAuth), <code>laravel/ai</code></td></tr><tr><td>GraphQL</td><td><code>webonyx/graphql-php</code></td></tr><tr><td>Sign-in</td><td>Passkeys (<code>spatie/laravel-passkeys</code>), authenticator apps, impersonation</td></tr></table>'),
            $this->seeAlso(['why-aurora' => 'Why Aurora', 'project-structure' => 'Structuring a project']),
        ]);
    }

    /* ── Using the admin ──────────────────────────────────────────────── */

    protected function blocksForFindingYourWay(): array
    {
        return $this->doc('finding-your-way', [
            B::rich('<p>The sidebar has the Dashboard, then three groups: <strong>Content</strong> (daily work), <strong>AI</strong>, and <strong>Administration</strong>, with Settings at the very end. Everyone sees only what their role allows.</p><h2>The dashboard</h2><ul><li><strong>Welcome</strong>: a greeting by time of day, quick actions (New page, Edit the homepage, View the website) shown only to people who may use them, and an AI card: <em>Connect your AI</em> until an app is connected, then the latest AI activity.</li><li><strong>Content statistics</strong>, refreshed every minute: live pages out of all pages, pages waiting for review and how many are scheduled, and edits and AI actions in the last 14 days.</li><li><strong>Recent changes</strong>: every page revision from every surface (form, visual editor, AI), with who, when and a link into the editor.</li><li><strong>Needs attention</strong>: pages in review and pages scheduled to go live, with one-click <strong>Publish</strong>. When nothing is waiting it says "All caught up".</li></ul>'),
            $this->screenshot('dashboard', 'The dashboard: quick actions, content statistics, recent changes and pages that need attention.'),
            B::rich('<h2>The command palette</h2><p>Press <kbd>⌘K</kbd> or <kbd>Ctrl+K</kbd> anywhere:</p><ul><li>actions: draft a page, edit the homepage, upload images, invite someone, create a redirect or a menu, and more;</li><li>commands for the page that\'s open;</li><li>every screen and every Settings tab;</li><li>records from global search;</li><li>light and dark mode.</li></ul>'),
            $this->screenshot('command-palette', 'The command palette: actions, screens and pages, from the keyboard.'),
            B::rich('<h2>Everywhere else</h2><ul><li><strong>Global search</strong> finds pages, files, forms, people and Settings tabs by name.</li><li><strong>Notifications</strong> in the bell, to the people who can act on them and in their own language: a page sent for review, your page published by someone else, a new form submission, an accepted invitation.</li><li><strong>The AI assistant</strong>, a chat bubble or <kbd>Ctrl+Shift+K</kbd>.</li><li><strong>The user menu</strong>: your profile, connected AI apps, the website, light or dark mode (when the theme has both) and sign out. Your theme and sidebar state follow you to any browser.</li><li><strong>Unsaved changes are never lost by accident</strong>: leaving a form or the editor asks first.</li><li><strong>Mobile friendly</strong>: the admin, Settings included, works on a phone.</li></ul>'),
        ]);
    }

    protected function blocksForPages(): array
    {
        return $this->doc('pages', [
            B::rich('<p>The heart of the CMS. The sidebar badge counts pages waiting for review.</p><h2>The list</h2><ul><li><strong>Tabs</strong>: All pages, Published, Scheduled, Review and Drafts, plus <em>Ended</em> (the unpublish date has passed) and <em>Changes waiting</em> (unpublished changes) when they have pages.</li><li><strong>Each row</strong> shows the full address on the site\'s domain; icons for the homepage, password protection and someone else editing; the status; and the layout, block count, author and last edit.</li><li><strong>Order</strong> [since 1.2.0]: by address, the homepage first and each page followed by its subpages. Sort by a column, such as the title, instead.</li><li><strong>Search and filters</strong>: by title and address; filter by status, layout, author and trashed.</li><li><strong>New page</strong> asks for a title, then opens the form with the address filled in. <em>Start from</em> copies another page\'s blocks as a template.</li><li><strong>Row actions</strong>: Design (visual editor), Publish, Settings, View or Preview, Create subpage, Duplicate, Delete and Restore.</li><li><strong>Bulk actions</strong>: Publish, Move to drafts, Reindex, Delete and Restore, each checked page by page against permissions.</li></ul>'),
            $this->screenshot('pages', 'The pages list, with its tabs, statuses and the last edit of every page.'),
            B::rich('<h2>The page form</h2><ul><li><strong>Page</strong>: title, parent page, address (with the domain, language prefix and parent path shown; reserved and duplicate addresses refused) and summary.</li><li><strong>Content</strong>: a preview of the page in its layout, with <em>Open visual editor</em>.</li><li><strong>Search engines</strong>: SEO checks, read from the page as visitors get it (every check is under <em>SEO checks</em> in these docs), a Google result preview, the SEO title and meta description, and <em>Hide from search engines</em>.</li><li><strong>Social sharing</strong>: the share image, cropped to 1200×630, and a social card preview.</li><li><strong>Publishing</strong>: status, publish and unpublish dates, <em>Use as homepage</em>, <em>Only for signed-in people</em> and <em>Password protected</em>.</li><li><strong>Languages</strong>, <strong>Layout</strong> (cards with thumbnails, and under them the chosen layout\'s own fields, such as a background image) and <strong>Details</strong>: the author, the last editor, revisions and search indexing.</li></ul>'),
            $this->screenshot('page-seo', 'Search engines: SEO checks, a Google result preview, and the SEO title and description with Write with AI.'),
            B::rich('<h2>Around the form</h2><ul><li><strong>Language switcher</strong>: edit the page\'s text in each language.</li><li><strong>Preview</strong>: a signed link that shows a draft, a scheduled page or unpublished changes inside the real site.</li><li><strong>Edit locks</strong>: opening a page takes a lock, renewed every 20 seconds and dropped after 60 seconds without renewal. Others see who is editing and can <em>Take over</em>; saves from anyone else are refused while the lock is held.</li><li><strong>Changing the address</strong> of a live page asks whether to keep the old address working, then creates 301 redirects in every language. Subpages follow.</li><li><strong>History</strong>: every revision with its source (Editor, Visual editor, MCP, Assistant, Restore or System), the person and a summary, a preview of any revision, and one-click restore.</li><li><strong>Duplicate</strong>: a draft copy with the same content, layout and translations.</li></ul>'),
            $this->screenshot('history', 'A page\'s history: who changed it, from where, and a restore button for every revision.'),
            $this->seeAlso(['publishing' => 'Publishing workflow', 'visual-editor' => 'The visual editor', 'seo' => 'SEO checks']),
        ]);
    }

    protected function blocksForPublishing(): array
    {
        return $this->doc('publishing', [
            B::rich('<h2>Statuses</h2><p>Pages are <strong>Draft</strong>, <strong>Review</strong> or <strong>Published</strong>. Authors send pages for review; people with the publish permission publish them, from the page, the list, the dashboard or a notification. People who can\'t publish only see <em>Submit for review</em>.</p><h2>Publishing is checked on the model</h2><p>Taking a page live needs permission, whether by status, date or making it the homepage. No surface can skip it: forms, bulk actions and AI all go through the same check.</p><h2>Scheduling</h2><p>A future publish date schedules the page; an unpublish date takes it down again by itself. A page is live when its publish date has passed and its unpublish date hasn\'t, worked out on every request: there\'s no cron job to forget.</p><h2>Unpublished changes</h2><ul><li>Saving a live page keeps the changes off the website until someone publishes them. A banner says so, who saved them and when, with <em>Publish changes</em>, <em>Preview</em> and <em>Discard</em>.</li><li>Previews show the changes, and AI tools read and edit them.</li><li>The address and the publishing settings still apply straight away.</li><li>A site can turn this off under <strong>Settings › Website › Publishing</strong> ("Changes go live when they\'re saved"), but only for people allowed to publish. Everyone else\'s changes still wait.</li></ul>'),
            $this->screenshot('unpublished', 'Saved, but not on the website yet: Publish changes, Preview or Discard.'),
            B::rich('<h2>Revisions and locks</h2><ul><li><strong>Revisions</strong>: every save from every surface, restorable with one click. Restoring keeps the current version in the history.</li><li><strong>Edit locks</strong>: one person edits a page at a time; locks clear themselves within a minute of the editor leaving.</li><li><strong>Notifications</strong>: a page sent for review notifies everyone who may publish it; its author hears when it goes live.</li></ul>'),
        ]);
    }

    protected function blocksForVisualEditor(): array
    {
        return $this->doc('visual-editor', [
            B::rich('<p>Open it with <strong>Design</strong> in the pages list, or <em>Open visual editor</em> on the page form.</p>'),
            $this->screenshot('editor', 'The visual editor: blocks on the left, the page on the canvas, and the selected block\'s fields on the right.'),
            B::rich('<h2>The panels</h2><ul><li><strong>Blocks</strong>: a palette of blocks, grouped by category and searchable, each with a picture of what it looks like. Click a block to add it, or drag it onto the canvas, into a band, a column or a grid.</li><li><strong>Structure</strong>: an outline of the page to select, move and nest blocks.</li><li><strong>Inspector</strong>: the selected block\'s fields, which update the canvas as they change. Every block also has design options: padding, background, width and alignment.</li></ul><h2>On the canvas</h2><ul><li><strong>Inline editing</strong>: headings, paragraphs, button labels, and items inside lists, such as an FAQ question or a pricing feature. <code>*Asterisks*</code> highlight words in headings.</li><li><strong>Rich text</strong> [since 1.2.0] is edited right on the page too, where a block allows it: bold, italic, underline, strikethrough, links, H2 and H3 headings, lists and quotes. Text with tables, images or alignment is edited in the inspector.</li><li><strong>Images</strong>: clicking an empty image opens the media picker. Each use of an image can be cropped, rotated, flipped and adjusted without changing the original.</li><li><strong>Embeds</strong>: drop a YouTube, Vimeo, map or other allowed link onto an Embed block.</li><li><strong>Editing tools</strong>: desktop, tablet and mobile widths, which show the website\'s own tablet and mobile layouts; undo and redo with keyboard shortcuts; copy and paste blocks and styles; resize blocks and columns; a right-click menu.</li><li><strong>Preview</strong> renders the unsaved page inside the real website: header, menus, footer and CSS.</li></ul>'),
            $this->screenshot('editor-mobile', 'The same page at mobile width, with the structure outline open.'),
            $this->note('Rich text on the canvas, for now', 'It opens with a click, not yet from the keyboard. Links are typed as an address in a prompt, without the inspector\'s page and file picker: pick a page or file in the inspector.'),
            B::rich('<h2>Saving</h2><ul><li>The status shows <em>Unsaved changes</em>, <em>Saved</em>, or <em>Saved, not published</em> when the page has unpublished changes, with a <strong>Publish</strong> button.</li><li>Saves are recorded as "Visual editor" revisions and respect edit locks.</li><li>Leaving with unsaved changes offers <em>Discard</em> or <em>Save and leave</em>.</li></ul><h2>Translating</h2><ul><li>Open the language menu, or add <code>?language=nb</code>. All languages share one block structure, and in another language only the text can be edited.</li><li>Untranslated text is highlighted. Text that stays the same in a language, such as a name, is marked <em>Same in …</em> [since 1.2.0] in the inspector, and then counts as translated.</li><li><em>Translate with AI</em> fills in what\'s missing, or translates everything, including the title, summary and SEO fields.</li></ul><h2>And more</h2><ul><li><strong>Shortcodes</strong>: the built-in ones (<code>[site_name]</code>, <code>[page_count]</code>) and the site\'s own, written under Settings › Website › Shortcodes [since 1.2.0]. <strong>Code blocks</strong>, which are only for people with the <em>code blocks</em> permission.</li><li><strong>The AI assistant</strong> can change the page while it\'s open; the canvas redraws without reloading.</li></ul>'),
            $this->seeAlso(['canvas-styles' => 'The canvas shows your CSS', 'creating-blocks' => 'Creating blocks']),
        ]);
    }

    protected function blocksForMediaLibrary(): array
    {
        return $this->doc('media-library', [
            B::rich('<p>One library shared by every site, organised like a file manager.</p><h2>Browsing and uploading</h2><ul><li><strong>Folders and breadcrumbs</strong>, with the open folder kept in the address so it can be bookmarked, and the browser\'s back button returns to the folder before [since 1.2.4]. Inside a folder, the arrow beside its name goes up one level [since 1.2.3]. <em>Library</em> opens a tree of every folder to jump to, and the open folder\'s name the folders next to it; the folders between them open with a click. A grid of cards or a list of rows. Search and tags look across every folder.</li><li><strong>Folders</strong>: create, rename, move and delete them; deleting moves the contents up a level. Drag files and folders onto folders or breadcrumbs.</li><li><strong>Folders among the files</strong> [since 1.2.5]: a folder\'s folders are the first cards of the grid, or the first rows of the list, as on a computer. The list sorts from the toolbar. To show folders in a row of their own above the files instead, switch off <em>Show folders among the files</em> under <strong>Settings › Media › Media library</strong>.</li><li><strong>Uploading</strong>: a dialog, or drag and drop from the desktop, with progress per file. Images, video, audio, PDF, Office documents, text and archives, up to 10 MB per image and 50 MB per other file unless the site sets other limits [since 1.1.0] (see Configuration). Scripts, HTML and executables are refused.</li><li><strong>Tags</strong>: coloured tags shown as dots, created on the spot. <strong>Filters</strong>: type, tags, used or unused, and with or without alt text.</li><li><strong>Bulk actions</strong>: move, access and delete, and set the credit or write alt text with AI for the images that have none [since 1.2.3]. Everything is also on the right-click menu.</li></ul>'),
            $this->screenshot('media', 'The media library: folders, breadcrumbs and images with their tags.'),
            B::rich('<h2>A file\'s page</h2><ul><li>A preview with a <strong>focal point</strong> to click, which cropped sizes keep in view.</li><li>Title, alt text and caption per language, and credit, filled in from a photo\'s EXIF Artist, or Copyright, when it\'s added [since 1.2.5]. <em>Write with AI</em> looks at the image to write the alt text, and the assistant can write it for every new image by itself.</li><li>Folder and tags, file details and a download, EXIF data for images and ID3 tags for audio.</li><li><strong>Replace file</strong>: the file keeps its id, so every page, link and redirect that uses it keeps working.</li><li><strong>Used on</strong>: every page and redirect that uses the file, tracked automatically.</li><li><strong>Formats</strong>: every size made from an image with its status, regenerate one size or all, and each block\'s own edited version.</li><li><strong>Access</strong>: limit a file or folder to roles or people, with read or read and write access. This only narrows the admin; files still show wherever pages use them.</li></ul>'),
            $this->screenshot('media-file', 'An image\'s page: the focal point, alt text per language, folder and tags.'),
            B::rich('<h2>Image formats</h2><p>Under <strong>Settings › Media</strong>: name, handle, width, height, WebP, AVIF, JPEG or PNG, quality, and whether a format is part of responsive <code>srcset</code>s. Changing a format brings existing images up to date in the background. Small, medium, large, thumbnail and share image come built in.</p><h2>In templates</h2><p>Every file has a lasting address, <code>/files/{id}</code>, even after it\'s replaced. In Blade, show an image with its responsive sizes, the block\'s crop and the focal point:</p>'),
            B::code('<x-aurora::media :value="$data[\'image\'] ?? null" size="md" sizes="(min-width: 1024px) 50vw, 100vw" />', 'blade'),
            B::rich('<p>Or get one size\'s address with <code>AssetLibrary::url($value, \'md\')</code>. The media disk comes from Spatie Media Library (<code>media-library.disk_name</code>); local and S3 both work.</p>'),
        ]);
    }

    protected function blocksForForms(): array
    {
        return $this->doc('forms', [
            B::rich('<h2>Building a form</h2><ul><li><strong>Field types</strong>: short text, email, phone, long text, dropdown, single choice, multiple choice, date, file upload (with allowed types and a size limit) and a consent checkbox. Each field has a label, key, placeholder, help text, required and width.</li><li><strong>Conditional fields</strong>: <em>Show only when</em> another field has a given answer. Hidden fields aren\'t required, checked or stored.</li><li><strong>Translations</strong>: every field\'s text in each language, with one-click AI translation.</li><li><strong>Settings</strong>: whether the form accepts answers, the button label, a thank-you message or a page to go to afterwards, and email addresses to notify.</li></ul>'),
            $this->screenshot('forms', 'The form builder: fields, their widths, and the form\'s settings.'),
            B::rich('<h2>Submissions</h2><ul><li>Statuses New, Read, Archived and Spam; reply by email; bulk status changes; export to CSV.</li><li>Uploaded files are served through signed links.</li><li>The sidebar badge counts new submissions, and everyone who handles forms is notified.</li></ul>'),
            $this->screenshot('submissions', 'Submissions, ready to read, reply to, archive or export.'),
            B::rich('<h2>Spam protection</h2><p>A honeypot field, a minimum fill time and a rate limit. Spam is kept, marked as spam, and sends no notifications.</p><h2>On the website</h2><p>Place a form with the <em>Form</em> block. Submissions work without JavaScript: they\'re a plain POST to <code>/forms/{form}</code> with server-side validation, and messages and old input come back to the page. Conditional fields are shown and hidden in the browser and checked again on the server. Deleting a form warns which live pages use it.</p><p>To send submissions anywhere else, listen for <code>Aurora\Events\FormSubmitted</code> or add a webhook.</p>'),
            $this->seeAlso(['webhooks' => 'Webhooks and events']),
        ]);
    }

    protected function blocksForMenusRedirectsTrash(): array
    {
        return $this->doc('menus-redirects-trash', [
            B::rich('<h2>Menus</h2><ul><li><strong>Building a menu</strong>: drag items to reorder them, or onto another item to nest them, as deep as you like. Add pages (optionally with their subpages), shortcuts, custom links and plain text labels. Items can have icons, CSS classes and <code>rel</code>.</li><li><strong>Page items</strong> link to the page by id, so they follow the page when its address changes, and use its title unless the item has its own. Titles are per language.</li><li><strong>Menu locations</strong> (<em>More › Manage locations</em>, or Settings › Templates) are named places in the templates, such as <code>header</code> and <code>footer</code>, that a site adds and renames itself. Templates show a location\'s menu with <code>Menu::location(\'header\')</code>.</li></ul>'),
            $this->screenshot('menu', 'The menu editor: nested items, linked to pages.'),
            $this->note('The menu beside this page', 'The docs menu you see on this page is an ordinary menu in the "docs" location. Editors reorder the docs by dragging its items.', 'brand'),
            B::rich('<h2>Redirects</h2><ul><li><strong>Manual redirects</strong> go from an old address to a page (which they follow when its address changes), a media file (which survives replacing the file) or any URL. Permanent (301) or temporary (302); old addresses may contain dots, such as <code>/brochure.pdf</code>.</li><li><strong>Automatic redirects</strong> are made when a live page\'s address changes, in every language.</li><li><strong>The list</strong> shows hit counts and when each redirect was last used, and flags redirects to pages in the trash. Redirects are checked before a 404.</li></ul>'),
            $this->screenshot('redirects', 'Redirects, with where they go and how often they\'re used.'),
            B::rich('<h2>Trash</h2><ul><li>Deleted pages, media and forms, one tab each. The sidebar entry only appears while something is in the trash.</li><li>Restore, preview or delete permanently, also in bulk, or <em>Empty trash</em>.</li><li>Before deleting permanently it explains what happens: a page goes with its history, a form with its submissions, and media leaves an empty spot on the pages that used it.</li></ul>'),
        ]);
    }

    /* ── Building a site ──────────────────────────────────────────────── */

    protected function blocksForProjectStructure(): array
    {
        return $this->doc('project-structure', [
            B::rich('<p>Keep the CMS out of your app and your site out of the CMS.</p><table><tr><th>What</th><th>Where</th></tr><tr><td>Your blocks\' classes</td><td><code>app/Blocks/</code> (found automatically; see <code>aurora.block_path</code>)</td></tr><tr><td>Your blocks\' views</td><td><code>resources/views/components/blocks/{type}.blade.php</code></td></tr><tr><td>Page layouts</td><td><code>resources/views/vendor/aurora/layouts/</code> (published), or any Blade view you name in Settings › Layouts</td></tr><tr><td>The site\'s shell (header, footer, menus)</td><td><code>resources/views/components/layouts/</code> and <code>components/site/</code></td></tr><tr><td>Website CSS</td><td><code>resources/css/app.css</code> (Tailwind 4), also used on the editor\'s canvas</td></tr><tr><td>Design tokens (colours, fonts)</td><td><code>resources/css/site/tokens.css</code>, imported by <code>app.css</code></td></tr><tr><td>Site set-up: settings tabs, block order</td><td><code>app/Providers/AppServiceProvider.php</code></td></tr><tr><td>Overrides of Aurora\'s public views</td><td><code>resources/views/vendor/aurora/</code></td></tr></table><p>Read content in templates through the models and helpers Aurora gives you: <code>Menu::location(\'header\')</code>, <code>Setting::get(\'site_name\')</code> and <code>$page->url()</code>.</p>'),
            $this->note('This website is built that way', 'The docs layout you\'re reading is a view in the app (resources/views/layouts/docs.blade.php), added as a layout under Settings › Templates › Layouts. Its sidebar is Menu::location(\'docs\').', 'brand'),
            $this->seeAlso(['creating-blocks' => 'Creating blocks', 'layouts' => 'Layouts']),
        ]);
    }

    protected function blocksForCreatingBlocks(): array
    {
        return $this->doc('creating-blocks', [
            B::rich('<p>A block is <strong>one PHP class and one Blade view</strong>. The same view renders on the website, on the editor\'s canvas and in previews.</p>'),
            B::code(<<<'BASH'
php artisan aurora:make-block "Pricing table" --category=marketing --icon=phosphor-currency-circle-dollar
php artisan aurora:make-block "Two columns" --container      # a block that holds other blocks
BASH, 'bash'),
            B::rich('<p>This creates <code>app/Blocks/PricingTableBlock.php</code> and <code>resources/views/components/blocks/pricing-table.blade.php</code>. Blocks in <code>app/Blocks</code> are registered automatically. The class says what the block is:</p>'),
            B::code(<<<'PHP'
namespace App\Blocks;

use Aurora\Blocks\Block;
use Aurora\Blocks\Editable;
use Aurora\Blocks\Fields;
use Aurora\Filament\Forms\Components\AssetPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class CardBlock extends Block
{
    public static function type(): string { return 'card'; }               // stored in the page JSON
    public static function label(): string { return __('Card'); }
    public static function description(): string { return __('Image, title, text and a link in a box.'); }
    public static function icon(): ?string { return 'phosphor-identification-card'; }
    public static function category(): string { return 'content'; }         // palette group

    // The inspector's fields: any Filament form components.
    public static function schema(): array
    {
        return [
            AssetPicker::make('image')->label(__('Image')),                     // a media library image
            TextInput::make('title')->label(__('Title')),
            Textarea::make('body')->label(__('Text'))->rows(3),
            ...Fields::link(),                                                  // page, file or URL
        ];
    }

    // What a new block starts with.
    public static function defaults(): array
    {
        return ['title' => 'Card title', 'body' => 'Why this matters.'];
    }

    // Fields editors type into right on the canvas.
    public static function editables(): array
    {
        return [
            'title' => Editable::text()->placeholder(__('Title')),
            'body' => Editable::text()->multiline(),
        ];
    }

    // Told to AI assistants: when to use it, and how.
    public static function aiHints(): ?string
    {
        return 'Normally placed inside a "grid". The card becomes clickable when it has a link.';
    }
}
PHP, 'php', 'app/Blocks/CardBlock.php'),
            B::rich('<h2>Text editors change on the canvas</h2><p><code>editables()</code> lists the fields editors type into right on the page. Mark each one\'s element in the view with <code>@editable(\'field\')</code>.</p><ul><li><code>Editable::text()</code>: a line of text; <code>->multiline()</code> keeps line breaks, and <code>->placeholder()</code> is shown while it\'s empty.</li><li><code>Editable::richText()</code> [since 1.2.0], for a <code>RichEditor</code> field: bold, italic, underline, strikethrough, links, H2 and H3 headings, lists and quotes, right on the page. Text with tables, images or alignment is edited in the inspector only.</li></ul>'),
            B::code(<<<'PHP'
public static function editables(): array
{
    return [
        'title' => Editable::text()->placeholder(__('Title')),
        'body' => Editable::richText(),          // a RichEditor field
    ];
}
PHP, 'php'),
            B::code(<<<'BLADE'
<h2 @editable('title')>{{ inline_text($data['title'] ?? '') }}</h2>
<div @editable('body')>{{ rich_text($data['body'] ?? '') }}</div>
BLADE, 'blade'),
            B::rich('<h2>Editable items in a list [since 1.2.1]</h2><p>Text inside a repeater\'s items, such as the questions in an FAQ, is made editable the same way. Declare the item fields with <code>itemEditables()</code>, keyed by the repeater\'s name, then the field\'s:</p>'),
            B::code(<<<'PHP'
public static function itemEditables(): array
{
    return [
        'items' => [
            'question' => Editable::text()->placeholder(__('Question')),
            'answer' => Editable::text()->multiline(),
        ],
    ];
}
PHP, 'php'),
            B::rich('<p>In the view, mark each item\'s element with <code>@editableItem</code>: the repeater\'s name, the item\'s position and the field\'s name.</p>'),
            B::code(<<<'BLADE'
@foreach ($data['items'] ?? [] as $item)
    <h3 @editableItem('items', $loop->index, 'question')>{{ $item['question'] ?? '' }}</h3>
    <p @editableItem('items', $loop->index, 'answer')>{{ $item['answer'] ?? '' }}</p>
@endforeach
BLADE, 'blade'),
            B::rich('<ul><li>It works like <code>@editable</code>: on the canvas it makes the element editable, and on the website and in previews it outputs nothing.</li><li>Only fields listed in <code>itemEditables()</code> can be edited. Marking an element that isn\'t declared there does nothing.</li><li>If the view filters or reorders the items, pass the item\'s position in the saved list, not its position in the loop.</li></ul><p>Views that call <code>InlineEditing::itemAttributes()</code>, as before, keep working.</p>'),
            B::rich('<h2>More a block can say</h2><table><tr><th>Method</th><th>What it does</th></tr><tr><td><code>itemEditables()</code></td><td>Inline editing inside repeater items, such as FAQ questions</td></tr><tr><td><code>sameInEveryLanguage()</code></td><td>Fields that aren\'t translated (links, numbers, settings)</td></tr><tr><td><code>preview()</code></td><td>A picture of the block for the palette (or upload one in Settings › Blocks)</td></tr><tr><td><code>isVisible()</code></td><td>Hide a block from some people, like code blocks</td></tr></table><h2>Container blocks</h2><p>Blocks that hold other blocks implement <code>Aurora\Blocks\Contracts\Container</code>, declare their slots, and draw each one with <code>block_slot()</code>:</p>'),
            B::code(<<<'PHP'
use Aurora\Blocks\Contracts\Container;

class BandBlock extends Block implements Container
{
    public static function slots(array $data): array { return ['content']; }
    // …
}
PHP, 'php'),
            B::code('<section class="band">{{ block_slot(\'content\') }}</section>', 'blade'),
            B::rich('<h2>Ordering and registering by hand</h2><p>Blocks from other folders, or in a set palette order, are registered from a service provider: <code>Blocks::register([HeroBlock::class, CardBlock::class])</code>. A type registered again replaces the earlier one, so a site can override a library block.</p><h2>Leaving out the page builder\'s own blocks [since 1.2.0]</h2><p>The page builder\'s own blocks (section, text, list, quote, image, button, video, embed, spacer, divider, shortcode and code) are drawn in its own stylesheet, which a site\'s layout doesn\'t load. Leave out the ones your stylesheet doesn\'t style with <code>Blocks::forget(\'text\', \'quote\', \'divider\')</code> in a service provider. Registering a type again brings it back.</p><h2>What you get for free</h2><ul><li>validated when AI tools write it;</li><li>described to AI assistants by <code>list_block_types</code>, built from the schema;</li><li>translated block by block, and indexed for search;</li><li>served structured by the REST and GraphQL APIs: images, page links and forms come out as what they point at.</li></ul><h2>This website\'s blocks</h2><p>This website is built from 24 blocks of its own, in its <code>app/Blocks</code>. They\'re good examples to read or start from.</p><table><tr><th>Category</th><th>Blocks</th></tr><tr><td>Layout</td><td>Section band, Responsive grid, Button group</td></tr><tr><td>Content</td><td>Heading, Paragraph, Rich text, Button, Card, Callout, Accordion / FAQ, Tabs, Steps / timeline</td></tr><tr><td>Marketing</td><td>Hero, Feature, Statistic, Pricing plan, Testimonial, Team member, Logo cloud, Call to action</td></tr><tr><td>Media</td><td>Image, Gallery</td></tr><tr><td>Dynamic</td><td>Form, Page list</td></tr><tr><td>Developer</td><td>Code sample</td></tr></table><p>The page builder adds Section (columns), List, Quote, Spacer, Divider, Video, Embed, Shortcode and Code.</p>'),
            B::buttons([
                B::button('Block views and helpers', $this->id('block-views'), 'primary', size: 'sm', icon: 'arrow-right'),
                B::button('See every block', $this->id('components'), 'outline', size: 'sm'),
            ]),
        ]);
    }

    protected function blocksForBlockViews(): array
    {
        return $this->doc('block-views', [
            B::rich('<p>A block\'s view is an anonymous Blade component. It receives the block\'s <code>$data</code>:</p>'),
            B::code(<<<'BLADE'
@props(['data' => []])

<a href="{{ link_href($data) }}" class="card">
    <x-aurora::media :value="$data['image'] ?? null" size="md" class="card-image" />
    <h3 @editable('title')>{{ inline_text($data['title'] ?? '') }}</h3>
    @if (shows_field('body', $data['body'] ?? null))
        <p @editable('body')>{{ plain_text($data['body'] ?? '') }}</p>
    @endif
</a>
BLADE, 'blade', 'resources/views/components/blocks/card.blade.php'),
            B::rich('<h2>The helpers</h2><p>The helpers are short forms of static methods (<code>inline_text()</code> is <code>Aurora\Support\Markup::inline()</code>), so views need no <code>@php use …</code> imports. Call the classes directly when you prefer.</p><table><tr><th>Helper</th><th>What it does</th></tr><tr><td><code>@editable(\'field\')</code></td><td>Makes the element editable on the canvas (for fields listed in <code>editables()</code>)</td></tr><tr><td><code>@editableItem(\'items\', $index, \'field\')</code> [since 1.2.1]</td><td>The same for a field inside a repeater\'s item (for fields listed in <code>itemEditables()</code>)</td></tr><tr><td><code>inline_text($text)</code></td><td>Text with <code>*highlighted*</code> words</td></tr><tr><td><code>plain_text($text)</code></td><td>Plain text, escaped</td></tr><tr><td><code>rich_text($html)</code></td><td>Rich text from a RichEditor, cleaned</td></tr><tr><td><code>shows_field($field, $value)</code></td><td>Whether to render an optional field: when it\'s filled, or when the canvas is editing so it can be filled</td></tr><tr><td><code>link_href($data)</code>, <code>link_target($data)</code></td><td>The address and target of a <code>Fields::link()</code> group; pass the prefix for another group: <code>link_href($data, \'cta\')</code></td></tr><tr><td><code>is_editing()</code></td><td>True on the canvas, for example to show placeholders</td></tr><tr><td><code>block_slot($name)</code></td><td>A container block\'s slot with the blocks in it</td></tr><tr><td><code>current_page()</code> [since 1.2.0]</td><td>The page being drawn, on the website, in previews and on the canvas; null in the palette\'s preview of a block</td></tr><tr><td><code>block_depth()</code> [since 1.2.0]</td><td>How deep the block sits: 0 straight on the page, 1 in a column or band, and so on</td></tr><tr><td><code>is_nested_block()</code> [since 1.2.0]</td><td>Whether the block is inside another block</td></tr><tr><td><code>&lt;x-aurora::media&gt;</code></td><td>A media library image, responsive, with the block\'s crop and the focal point</td></tr></table><h2>Or as Blade directives</h2><table><tr><th>Helper</th><th>Directive</th></tr><tr><td><code>{{ inline_text($data[\'title\'] ?? \'\') }}</code></td><td><code>@inlineText($data[\'title\'] ?? \'\')</code></td></tr><tr><td><code>{{ plain_text(…) }}</code>, <code>{{ rich_text(…) }}</code></td><td><code>@plainText(…)</code>, <code>@richText(…)</code></td></tr><tr><td><code>{{ link_href($data) }}</code>, <code>{{ link_target($data) }}</code></td><td><code>@linkHref($data)</code>, <code>@linkTarget($data)</code></td></tr><tr><td><code>{{ block_slot(\'content\') }}</code></td><td><code>@blockSlot(\'content\')</code> (not <code>@slot</code>: Blade\'s components use it)</td></tr><tr><td><code>@if (shows_field(\'body\', …)) … @endif</code></td><td><code>@shows(\'body\', …) … @endshows</code></td></tr><tr><td><code>@if (is_editing()) … @endif</code></td><td><code>@editing … @else … @endediting</code></td></tr><tr><td><code>{{ block_depth() }}</code>, <code>@if (is_nested_block()) … @endif</code></td><td><code>@blockDepth</code>, <code>@nested … @endnested</code> [since 1.2.0]</td></tr></table><h2>Drawing blocks</h2><p><code>&lt;x-aurora::blocks :page="$page" /&gt;</code> draws a page\'s blocks in the language being shown. Attributes go on the element around them, and <code>:blocks</code> draws a list of blocks instead of a page\'s (a container left out takes its blocks with it):</p>'),
            B::code(<<<'BLADE'
<x-aurora::blocks :page="$page" class="flex flex-col gap-24" />

<x-aurora::blocks :blocks="$blocks" />
BLADE, 'blade'),
            $this->note('Named so they never clash', 'The helpers are named for what they read (link_href, plain_text), never bare words like href() or text() that Laravel or another package could also define.'),
            $this->seeAlso(['blade-helpers' => 'Every Blade helper', 'canvas-styles' => 'The canvas shows your CSS', 'add-your-own-block' => 'Guide: add your own block']),
        ]);
    }

    protected function blocksForCanvasStyles(): array
    {
        return $this->doc('canvas-styles', [
            B::rich('<p>The visual editor renders your blocks in the website\'s own stylesheet, so they look on the canvas exactly as they do on the site. There\'s nothing to set up: Aurora finds <code>resources/css/app.css</code> through Vite (the built file, or the dev server while <code>npm run dev</code> runs) and scopes it to the canvas with CSS <code>@scope</code>, so it can\'t touch the admin around it.</p><h2>How your CSS is scoped</h2><ul><li>The stylesheet is wrapped in <code>@scope (.fpb-canvas)</code> (<code>Aurora\Support\CanvasStyles</code>).</li><li><code>:root</code>, <code>html</code>, <code>body</code> and <code>.fpb-canvas</code> are all the canvas: whatever your stylesheet says of the page itself, it says of the canvas.</li><li>Combinators are kept, so rules match as they do on the website: <code>body &gt; .x</code> matches only an <code>.x</code> straight on the canvas, not one inside a block [since 1.2.0].</li></ul><h2>Styling blocks on the canvas</h2><p>On the canvas, each block is wrapped in <code>.fpb-block</code>, with its type in <code>data-type</code> and its own markup in <code>.fpb-block-body</code>. Target them with <code>:scope &gt; .fpb-block</code> (a block straight on the page), <code>.fpb-block[data-type="hero"]</code> and <code>.fpb-block-body</code>. On the website, <code>:scope</code> at the top level is <code>:root</code>, so these rules do nothing there. This website gives a block dropped straight onto the page its gutters this way:</p>'),
            B::code(<<<'CSS'
/* On the website… */
.fpb-page > .fpb-el:not([data-block='band'], [data-block='hero']),
/* …and on the canvas. */
:scope > .fpb-block:not([data-type='band'], [data-type='hero']) > .fpb-block-body {
    max-width: 72rem;
    margin-inline: auto;
}
CSS, 'css', 'resources/css/site/tokens.css'),
            B::rich('<h2>Colours [since 1.2.0]</h2><p>The page\'s background and text colour, set on <code>html</code> or <code>body</code> (in a CSS layer or not), become the canvas\'s <code>--fpb-canvas-bg</code> and <code>--fpb-canvas-fg</code>. To choose them yourself, set those two on <code>html</code>:</p>'),
            B::code(<<<'CSS'
html {
    --fpb-canvas-bg: var(--color-zinc-50);
    --fpb-canvas-fg: var(--color-zinc-700);
}
CSS, 'css', 'resources/css/app.css'),
            B::rich('<h2>Widths [since 1.2.0]</h2><p>Width breakpoints (<code>@media (width &gt;= 48rem)</code>, <code>min-width</code>, <code>max-width</code>) are asked of the canvas, as container queries (<code>@container fpb-canvas</code>), so the editor\'s Tablet and Mobile widths show the site\'s tablet and mobile layouts. <code>vw</code> is the canvas\'s width (<code>cqw</code>). Other media queries, such as print, colour scheme and hover, stay as they are.</p><h2>Style the page in CSS, not with classes on &lt;body&gt;</h2><p>The canvas has no <code>&lt;body&gt;</code> of the site\'s, so put the page\'s font, colours and background in the stylesheet:</p>'),
            B::code(<<<'CSS'
@layer base {
    body {
        @apply bg-white font-body text-zinc-600;
    }
}
CSS, 'css', 'resources/css/app.css'),
            B::rich('<h2>Fonts</h2><p>Fonts in the stylesheet (<code>@font-face</code>, or an <code>@import url(…)</code> at the top) come along, and so do fonts loaded with Vite\'s font loading. Fonts linked from your layout\'s <code>&lt;head&gt;</code> don\'t, so load them in the stylesheet too, or the canvas shows them in a fallback font.</p>'),
            B::code("@import url('https://fonts.bunny.net/css?family=inter:400,500,600,700');\n@import 'tailwindcss';", 'css', 'resources/css/app.css'),
            B::rich('<h2>Another entry</h2><p>Set <code>AURORA_WEBSITE_CSS</code> (<code>aurora.website_css</code>) to another Vite entry, or leave it empty to show blocks without the site\'s styles.</p>'),
            $this->note('Coming from Aurora 1.0 or 1.1?', 'Since 1.2, body > .x on the canvas matches only an .x straight on the canvas, width breakpoints and vw follow the canvas rather than the admin window, and the page\'s colours carry over. A site can look a little different in the editor after updating.', 'warning'),
            $this->note('The admin theme is only the CMS\'s look', 'Blocks on the canvas are always in the website\'s own stylesheet, whichever admin theme is chosen. Nothing about your website is compiled into the admin.'),
        ]);
    }

    protected function blocksForLayouts(): array
    {
        return $this->doc('layouts', [
            B::rich('<p>A layout is a Blade view that draws a whole page: <code>&lt;head&gt;</code>, header, the blocks and the footer. Aurora ships four: <code>standard</code> (the default: header, menu and footer), <code>landing</code> (the header floating over the first block), <code>minimal</code> (the logo alone, for campaigns) and <code>blank</code> (the page alone), published to <code>resources/views/vendor/aurora/layouts</code> on install so the site can make them its own. This website adds two of its own: the landing page its homepage uses, and the docs layout you\'re reading.</p>'),
            B::code(<<<'BLADE'
<x-layouts.site
    :title="$page->seoTitle()"
    :description="$page->meta_description ?: $page->excerpt"
    :canonical="$page->url()"
    :noindex="$page->noindex || $page->isRestricted() || $preview || $embedded"
    :page="$embedded ? null : $page"
>
    <x-aurora::blocks :page="$page" />
</x-layouts.site>
BLADE, 'blade', 'resources/views/vendor/aurora/layouts/standard.blade.php'),
            B::rich('<ul><li><strong>Every layout gets</strong> <code>$page</code>, <code>$layout</code>, <code>$settings</code> (the layout\'s own fields, below), <code>$preview</code> (a preview link, or a page that isn\'t live yet) and <code>$embedded</code> (drawn inside the visual editor\'s preview).</li><li><strong><code>&lt;x-aurora::blocks :page="$page" /&gt;</code></strong> renders the page\'s blocks in the current language.</li><li><strong>Adding a layout</strong>: in <strong>Settings › Templates › Layouts</strong>, give it a name, handle, icon and the Blade view it uses, which can be any view in your app. <em>Make default</em> makes any layout the default, including those defined in code. Pages choose their layout from cards with thumbnails, and preview links can show a page in another layout.</li><li><strong>Shared pieces</strong> such as the header, menus and footer belong in components: <code>Menu::location(\'header\')</code>, <code>Setting::get(…)</code>, and the settings your blocks\' library adds.</li></ul><h2>What Aurora serves</h2><ul><li>Pages at their address, in the layout they chose, and other languages under their prefix (<code>/nb/om-oss</code>). Drafts and scheduled pages answer 404 to visitors and show as previews to signed-in editors.</li><li>Signed preview links for drafts, scheduled pages, unpublished changes, revisions, other languages and other layouts.</li><li><code>sitemap.xml</code> of live pages, without hidden, password-protected or members-only pages, and <code>robots.txt</code>, which keeps crawlers out of <code>/admin</code> and previews.</li><li>Password-protected pages unlock for the visit; members-only pages show a sign-in page. Publish <code>aurora-views</code> to restyle them.</li><li>Redirects, checked before a 404; the page cache; <code>/files/{id}</code>; and forms posted to <code>/forms/{form}</code>.</li></ul><p>Turn <code>aurora.routes</code> off to route pages yourself with <code>Aurora\Http\Controllers\PageController</code>. How addresses are matched, where the website\'s own routes fit in, and how they get the site\'s layout with <code>&lt;x-aurora::layout&gt;</code>, is under <em>Routing</em>.</p>'),
            B::rich('<h2>Fields of a layout\'s own [since 1.1.0]</h2><p>Some things a layout needs aren\'t blocks: a campaign layout\'s background image, say. Register fields for the layout\'s view from a service provider\'s <code>boot()</code>:</p>'),
            B::code(<<<'PHP'
use Aurora\Filament\Forms\Components\AssetPicker;
use Aurora\Support\Layouts;

Layouts::schema('layouts.campaign', fn (): array => [
    AssetPicker::make('background')
        ->label(__('Background image'))
        ->helperText(__('Shown behind the whole page.')),
]);
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
            B::rich('<ul><li>The page form shows them under the layout picker.</li><li>They\'re saved in the page\'s <code>settings</code> column, by view, so a field of the same name in another layout is kept apart.</li><li>The view reads its own from <code>$settings</code>, always with a fallback: pages saved before the fields existed don\'t have them.</li><li>Elsewhere, <code>$page-&gt;layoutSettings()</code> gives the page\'s values for its current layout, and <code>$page-&gt;layoutSetting(\'background\', $default)</code> one of them.</li><li>AI tools read and set them (<code>list_layouts</code>, <code>get_page</code>, and <code>layout_settings</code> on <code>create_page</code> and <code>update_page</code>), so give each field a clear label and help text.</li></ul>'),
            B::code(<<<'BLADE'
<x-layouts.site :page="$page">
    <div class="relative">
        @if ($settings['background'] ?? null)
            <x-aurora::media :value="$settings['background']" size="lg" class="absolute inset-0 -z-10 size-full object-cover" />
        @endif
        <x-aurora::blocks :page="$page" />
    </div>
</x-layouts.site>
BLADE, 'blade', 'resources/views/layouts/campaign.blade.php'),
            $this->note('Layout fields, for now', 'A layout field has one value for every language, and the REST and GraphQL APIs don\'t return layout fields.'),
            B::rich('<h2>Layouts in code [since 1.2.0]</h2><p>A layout can also be defined in code: a class extending <code>Aurora\Layouts\PageLayout</code>. <code>php artisan make:aurora-layout</code> [since 1.2.2] asks for a name and what the layout is for, and makes both halves: the class in <code>app/Layouts</code>, and its view in <code>resources/views/layouts</code>, started from the site\'s standard layout.</p>'),
            B::code('php artisan make:aurora-layout "Campaign" --description="Campaign pages: no menu, a big hero" --icon=phosphor-megaphone', 'bash'),
            B::code(<<<'PHP'
namespace App\Layouts;

use Aurora\Filament\Forms\Components\AssetPicker;
use Aurora\Layouts\PageLayout;

class CampaignLayout extends PageLayout
{
    public static function handle(): string { return 'campaign'; }    // what pages know it by
    public static function name(): string { return __('Campaign'); }
    public static function view(): string { return 'layouts.campaign'; }
    public static function description(): ?string { return __('No menu, a big hero.'); }
    public static function icon(): ?string { return 'phosphor-megaphone'; }

    // Fields of its own, as with Layouts::schema().
    public static function schema(): array
    {
        return [AssetPicker::make('background')->label(__('Background image'))];
    }
}
PHP, 'php', 'app/Layouts/CampaignLayout.php'),
            B::rich('<p>Layout classes in <code>app/Layouts</code> (<code>aurora.layout_path</code>) [since 1.2.2] are offered to editors on their own, as blocks in <code>app/Blocks</code> are. A layout from elsewhere, such as a component library, is registered from a service provider\'s <code>boot()</code>:</p>'),
            B::code("use Aurora\\Support\\Layouts;\n\nLayouts::register([CampaignLayout::class]);", 'php', 'app/Providers/AppServiceProvider.php'),
            B::rich('<ul><li>It\'s listed with the layouts added in the admin, and can be made the default there.</li><li>Its name, view and fields are the code\'s, so it can\'t be edited or deleted in the admin.</li><li>Changing <code>handle()</code> makes it a new layout: pages using the old handle don\'t follow.</li><li>Layouts in code need the migration that came with Aurora 1.2 (<code>php artisan migrate</code>).</li></ul>'),
        ]);
    }

    protected function blocksForYourOwnSettings(): array
    {
        return $this->doc('your-own-settings', [
            B::rich('<p>Add tabs to Settings from a service provider. Fields are saved with the other settings and read with <code>Setting::get()</code>.</p>'),
            B::code(<<<'PHP'
use Aurora\Support\SettingsTabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;

SettingsTabs::register(
    'opening-hours',
    fn (): Tab => Tab::make(__('Opening hours'))->schema([
        TextInput::make('opening_hours')->label(__('Opening hours')),
    ]),
    siteKeys: ['opening_hours'],      // each site keeps its own value
    page: 'website',                  // join Settings › Website (or null for a page of its own)
    publicKeys: ['opening_hours'],    // may be shown by the REST/GraphQL APIs
);
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
            B::code("<p>{{ \\Aurora\\Models\\Setting::get('opening_hours') }}</p>", 'blade'),
            $this->note('Private unless you say so', 'Settings are private unless a tab lists them in publicKeys. Never list keys, tokens or anything visitors shouldn\'t see.', 'warning'),
            B::rich('<p>This website adds the Header and Footer tabs under Settings › Website this way: the header button, the announcement bar, the footer note and social profiles.</p>'),
        ]);
    }

    protected function blocksForWebsiteScreens(): array
    {
        return $this->doc('website-screens', [
            B::rich('<p>A website often has more than pages: events, products, a team. Its own models and Filament screens join Aurora\'s admin from a service provider, through <code>Aurora\Support\AdminPanel</code>.</p><h2>Admin screens [since 1.2.0]</h2>'),
            B::code(<<<'PHP'
use Aurora\Support\AdminPanel;

// Every resource and page in a folder and its subfolders…
AdminPanel::discover(app_path('Filament/Resources'), 'App\Filament\Resources', perSite: true);

// …or name them.
AdminPanel::resources([EventResource::class], perSite: true);
AdminPanel::pages([Reports::class]);

// A sidebar group of their own: after Content, or after: 'AI', or after one of yours.
AdminPanel::navigationGroup('Events', fn (): string => __('Events'));
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
            B::rich('<ul><li><strong>Groups</strong>: a resource joins a group by its <code>$navigationGroup</code> name (<code>\'Events\'</code> above). Groups after the same one keep the order they\'re registered in.</li><li><strong>Each site\'s own records</strong>: the admin is per site (<code>/admin/{site}/…</code>). A website\'s resources show every site\'s records, unless they\'re registered with <code>perSite: true</code>, for a model with a <code>site</code> relation.</li><li><strong>Labels</strong>: name screens and fields with <code>__()</code>. A line in the app\'s <code>lang/{locale}.json</code> is the label straight away, and beats Aurora\'s own; only what\'s edited under Translations beats the app\'s file.</li></ul><h2>Published like a page [since 1.2.0]</h2><p>A model that is drafted, scheduled and published like a page uses <code>Aurora\Models\Concerns\HasPublishing</code>, with its columns in the migration:</p>'),
            B::code(<<<'PHP'
use Aurora\Models\Concerns\HasPublishing;

class Event extends Model
{
    use HasPublishing;
}

// In its migration: status, published_at and unpublish_at.
Schema::create('events', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('title');
    Event::publishingColumns($table);
    $table->timestamps();
});

// What visitors may see.
Event::query()->live()->get();
$event->isLive();
$event->isScheduled();
$event->hasEnded();
PHP, 'php'),
            B::rich('<p>Its resource gets the page form\'s publishing fields, the status column and the status filter from <code>Aurora\Filament\Publishing</code>:</p>'),
            B::code(<<<'PHP'
use Aurora\Filament\Publishing;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        TextInput::make('title')->label(__('Title')),
        Publishing::section(),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([TextColumn::make('title'), Publishing::column()])
        ->filters([Publishing::filter()]);
}
PHP, 'php', 'app/Filament/Resources/EventResource.php'),
            B::rich('<h2>Menu panels [since 1.2.0]</h2><p>The menu editor offers pages and custom links. Add panels for the website\'s own addresses with <code>Menus::panels()</code>: a <code>StaticMenuPanel</code> of fixed links, or a <code>ModelMenuPanel</code> for a model that implements <code>MenuPanelable</code>.</p>'),
            B::code(<<<'PHP'
use Aurora\Support\Menus;
use Datlechin\FilamentMenuBuilder\MenuPanel\StaticMenuPanel;

Menus::panels([
    StaticMenuPanel::make(__('Website'))->add(__('Events'), '/events'),
]);

// /events is in every language, under its prefix: in Norwegian the link goes to /no/events.
Menus::localized(['/events']);
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
            $this->seeAlso(['routing' => 'Routes of your own', 'your-own-settings' => 'Your own settings']),
        ]);
    }

    protected function blocksForSiteSearch(): array
    {
        return $this->doc('site-search', [
            B::rich('<h2>The engine</h2><p>Pages are indexed with Laravel Scout: title, summary, SEO fields and the text of every block, per language. Without a search service, Aurora\'s database engine is used: MySQL and MariaDB get full-text search with relevance, and other databases a simple match. Any Scout driver works too: set <code>SCOUT_DRIVER</code> to meilisearch, typesense or algolia, then import.</p><h2>On the website</h2><p><code>Page::search()</code> searches the live pages anyone may see on the site the visitor is on, worked out from the address, most relevant first:</p>'),
            B::code(<<<'PHP'
use Aurora\Models\Page;

$results = Page::search('fjord')->paginate(10);

// Add to it as with any Scout search: the limits stay.
Page::search('fjord')->query(fn ($query) => $query->with('author'))->get();

// Another site's pages, named.
Page::searchSite('fjord', site: $site)->get();
PHP, 'php'),
            B::rich('<p>Chaining <code>->query()</code> adds eager loading without lifting the limits: drafts and other sites\' pages never come back.</p><h2>In the admin</h2><p><strong>Settings › Search &amp; SEO</strong> tries searches and reindexes, and each page shows what the index holds for it. People who manage search can leave a page out. The REST and GraphQL APIs offer the same search.</p>'),
            B::rich('<h2>Rebuilding the index [since 1.0.4]</h2><p>Pages are indexed whenever they\'re saved. After importing content, changing pages outside the admin or moving to another search service, rebuild every page\'s entry with <code>php artisan aurora:reindex</code>: every site, or one with <code>--site</code>. Pages visitors can\'t find, such as drafts, are taken out of the index.</p>'),
            B::code("php artisan aurora:reindex\nphp artisan aurora:reindex --site=shop   # one site's pages", 'bash'),
            $this->screenshot('search', 'Settings › Search & SEO: try a search and see what the index finds.'),
        ]);
    }

    /* ── Running a site ───────────────────────────────────────────────── */

    protected function blocksForUsersAndRoles(): array
    {
        return $this->doc('users-and-roles', [
            B::rich('<h2>Users</h2><ul><li><strong>Inviting</strong>: <em>Invite people</em> sends several invitations at once, with a role and a personal note. The link works once and expires. Pending invitations can be copied, resent or revoked. <em>Add manually</em> is there too.</li><li><strong>The person</strong>: avatar, name, job title, email, password, admin language, the sites they work on, whether they\'re active (inactive people can\'t sign in or use AI) and their roles, though nobody can change their own.</li><li><strong>Sign-in</strong>: passkeys, offered after sign-in; authenticator apps for two-factor sign-in (several per person, each named) with recovery codes. An admin can reset someone\'s two-factor sign-in, and <em>Forgot password?</em> on the sign-in page emails a link to choose a new one.</li><li><strong>View as</strong> (impersonation): see the admin as someone else, never as a Super Admin. It needs a permission no role has by default.</li></ul>'),
            $this->screenshot('users', 'Users, with their roles, sites and how they sign in.'),
            B::rich('<h2>Roles and permissions</h2><ul><li><strong>Built-in roles</strong>: <strong>Super Admin</strong> (everything, always), <strong>Editor</strong> (every page, publishing, menus, media, forms and redirects), <strong>Author</strong> (their own pages, sent for review) and <strong>Viewer</strong> (looks around).</li><li><strong>Your own roles</strong>: add them in Users &amp; Roles › Roles, with a name and description per language, and pick what they may do in plain language. The list shows members and how much each role may do ("can do 12 of 30").</li><li><strong>The permissions</strong>: pages (view, create, edit own, edit any, publish, delete, restore, delete forever, manage search); media (view, edit, manage access); forms, menus and redirects (view, edit); people (users, roles, impersonation); site and AI (settings, activity log, sites, languages, translate, use the assistant, connect AI apps, code blocks).</li></ul>'),
            $this->screenshot('roles', 'A role\'s permissions, written for the people who pick them.'),
            B::rich('<h2>Sign-in rules</h2><p><strong>Settings › Security › Sign-in</strong> can require two-factor sign-in or passkeys for everyone.</p><h2>Activity log</h2><ul><li><strong>What it records</strong>: who created, changed, deleted or restored what, and which fields changed, across pages, menus, forms, redirects, media, folders, users, roles, layouts, menu locations, sites, languages, settings, webhooks, API keys and image formats.</li><li><strong>What it leaves out</strong>: what the system does by itself, and people\'s own preferences such as a collapsed sidebar or dark mode.</li><li><strong>Filters</strong>: who, kind, action and dates. Only people given the permission see it; no role has it by default.</li></ul>'),
            $this->screenshot('activity', 'The activity log: who changed what, and which fields.'),
            B::rich('<p>For developers: the policies are in <code>Aurora\Policies</code>, and the catalogue of permissions and roles is <code>Aurora\Support\Permissions</code>.</p>'),
        ]);
    }

    protected function blocksForLanguages(): array
    {
        return $this->doc('languages', [
            B::rich('<h2>Languages</h2><ul><li><strong>Adding languages</strong>: in Settings › Languages &amp; sites. The default language has no address prefix; the others get one, which may differ from the code (<code>nb</code> at <code>/no</code>).</li><li><strong>What\'s translated</strong>: page titles, addresses, summaries, SEO fields and block text, menu item titles, form field text, media alt text and captions, role names and every label.</li><li><strong>Per page</strong>: tick which languages it exists in. A page only answers in those, and links to an untranslated page go to its default language.</li><li><strong>The admin\'s language</strong> is each person\'s own choice, separate from the website\'s.</li></ul>'),
            $this->screenshot('languages', 'Languages: the default one, address prefixes and how much is translated.'),
            B::rich('<h2>A page\'s address in each language</h2><p>A page has its own address in every language: its <code>slug</code> is stored per language, and edited per language in the page form. In code:</p>'),
            B::code(<<<'PHP'
use Aurora\Support\ContentLocale;

$page->path('nb');            // /no/om-oss
$page->url('nb');             // https://example.com/no/om-oss
ContentLocale::current();     // the language being shown, such as nb
ContentLocale::prefix('nb');  // /no (empty for the default language)
PHP, 'php'),
            B::rich('<p>Routes of the website\'s own in more than one language are under <em>Routing</em>.</p><h2>What\'s translated in a block</h2><ul><li><strong>Translated</strong>: a block\'s text fields: text, multi-line text, rich text, Markdown, tags, and lists whose items have text.</li><li><strong>The same in every language</strong>: everything else, such as links, numbers, images, settings and lists of selected records.</li><li><strong>Text that isn\'t translated</strong>, such as a record\'s id or a handle typed into a text field, goes in the block\'s <code>sameInEveryLanguage()</code>.</li><li><strong>Same in …</strong> [since 1.2.0]: text that reads the same in a translation, such as a name, is marked so in the inspector, and then counts as translated.</li></ul><h2>Translations</h2><p>The <strong>Translations</strong> screen holds every label in the website and the admin, Aurora\'s, Laravel\'s and other packages\', editable in place, one tab per language, with a count of what\'s missing.</p><ul><li><strong>Filters</strong>: needs translation, where a label comes from, and edited here.</li><li><strong>Per label</strong>: edit every language at once, or reset to the lang file.</li><li><strong>For the whole list</strong>: <em>Translate all</em> with AI, <em>Import from lang files</em> (keeps edits) and <em>Remove unused</em>.</li><li>Missing labels fall back to the default language.</li></ul>'),
            $this->screenshot('translations', 'Every label, one tab per language.'),
            B::rich('<h2>Which label wins</h2><ol><li>A label edited under Translations in the admin.</li><li>The app\'s own <code>lang/{locale}.json</code>.</li><li>Aurora\'s and the other packages\' labels.</li></ol><p>So a site\'s own line in <code>lang/nb.json</code> is the label straight away, whatever Aurora\'s says, with nothing to import. On the command line, <code>aurora:sync-translations</code> adds new labels from the code, <code>aurora:import-translations</code> lists the lang files\' labels in the Translations screen (it only fills that screen) and <code>aurora:export-translations</code> writes edits made in the admin back to the lang files.</p>'),
            $this->seeAlso(['visual-editor' => 'Translating on the canvas', 'several-websites' => 'Several websites']),
        ]);
    }

    protected function blocksForSeveralWebsites(): array
    {
        return $this->doc('several-websites', [
            B::rich('<p><strong>Settings › Languages &amp; sites › Sites</strong> adds websites to the same admin.</p><ul><li><strong>Each site has its own</strong> domain, pages, menus, forms, redirects, settings, languages and people.</li><li><strong>Shared by all sites</strong>: media, users, roles, translations and the AI activity log.</li><li><strong>The admin</strong>: each site is at <code>/admin/{site}</code>, with a site switcher in the sidebar once there\'s more than one.</li><li><strong>The website</strong>: a request is answered by the site whose domain it came to. The default site answers every other domain.</li></ul><h2>In code</h2><p>Queries only see the current site\'s content. Use <code>CurrentSite::using($site, fn () => …)</code> to work on another site\'s, and <code>Page::searchSite($terms, site: $site)</code> to search it.</p>'),
            $this->seeAlso(['run-several-sites' => 'Guide: run several websites']),
        ]);
    }

    protected function blocksForSettings(): array
    {
        return $this->doc('settings', [
            B::rich('<p>Settings is grouped into pages in a side menu, each with tabs, at <code>?tab=&lt;page&gt;&amp;section=&lt;tab&gt;</code>. Packages and the website can add their own tabs.</p><table><tr><th>Page</th><th>Tab</th><th>What it holds</th></tr><tr><td><strong>Website</strong></td><td>Branding</td><td>Site name, contact email, tagline, default share image, brand and accent colours with a live preview</td></tr><tr><td></td><td>Publishing</td><td>Whether saved changes to live pages go live straight away</td></tr><tr><td></td><td>Shortcodes [since 1.2.0]</td><td>The site\'s own shortcodes, such as <code>[phone]</code>, and the text each prints</td></tr><tr><td></td><td>Header, Footer</td><td>From this website\'s own set-up: the header button, announcement bar, footer note and social profiles</td></tr><tr><td><strong>Templates</strong></td><td>Layouts</td><td>The page layouts: name, handle, icon, Blade view, and which is the default. Layouts defined in code are listed too, but can\'t be edited here [since 1.2.0]</td></tr><tr><td></td><td>Blocks</td><td>Every block, with an uploadable picture for the palette</td></tr><tr><td></td><td>Menu locations</td><td>The places in templates that can show a menu</td></tr><tr><td><strong>Media</strong></td><td>Media library [since 1.2.5]</td><td>Whether folders show among the files, or in a row of their own above them</td></tr><tr><td></td><td>Image formats</td><td>The sizes made of every image: width, height, format, quality, responsive</td></tr><tr><td><strong>Languages &amp; sites</strong></td><td>Languages, Sites</td><td>Languages and their prefixes; the websites this admin runs</td></tr><tr><td><strong>Search &amp; SEO</strong></td><td>Search index, SEO report</td><td>Try a search, reindex; every live page that could do better in search results</td></tr><tr><td><strong>Performance</strong></td><td>Page cache</td><td>Serve pages ready-made; how long to keep them; clear the cache</td></tr><tr><td><strong>Integrations</strong></td><td>REST API, GraphQL API, Webhooks</td><td>Switch the APIs on, require keys, manage keys; addresses told about content events</td></tr><tr><td><strong>Security</strong></td><td>Sign-in, Website access, API keys</td><td>Require two-factor or passkeys; limit the website to IP addresses; keys with scopes</td></tr><tr><td><strong>Appearance</strong></td><td>Admin theme</td><td>The admin\'s theme, for everyone, with each theme\'s version [since 1.1.0]</td></tr><tr><td><strong>AI Assistant</strong></td><td>AI Assistant</td><td>Provider, model, API key (stored encrypted), on or off, alt text for new images, instructions</td></tr><tr><td><strong>System</strong></td><td>Maintenance, Import, System info</td><td>Clear caches, send a test email, reindex; import WordPress; what the CMS runs on</td></tr></table>'),
            $this->screenshot('settings', 'Settings › Website › Branding, with the side menu of every settings page.'),
            B::rich('<h2>Shortcodes [since 1.2.0]</h2><p><strong>Settings › Website › Shortcodes</strong> holds the site\'s own shortcodes: a name, such as <code>[phone]</code>, and the text it prints. Editors use them in any text, next to the built-in <code>[site_name]</code> and <code>[page_count]</code>, and changing the text changes it everywhere at once.</p>'),
            $this->note('Shortcodes, for now', 'A shortcode of the site\'s own is the same on every site in the admin, and prints plain text only.'),
            $this->seeAlso(['your-own-settings' => 'Add your own settings']),
        ]);
    }

    protected function blocksForSeo(): array
    {
        return $this->doc('seo', [
            B::rich('<p>Aurora checks every page for what would help it in search results. The checks read the page as visitors get it, drawn with its layout, so headings, links and images in the layout and in every block count [since 1.2.0]. They\'re on the page\'s <strong>Search engines</strong> tab, in <strong>Settings › Search &amp; SEO › SEO report</strong> and for AI apps (<code>seo_report</code>).</p><h2>The checks</h2><table><tr><th>Check</th><th>Level</th></tr><tr><td>No meta description or summary, so search engines make up their own text</td><td>Problem</td></tr><tr><td>The title is longer than 60 characters</td><td>Warning</td></tr><tr><td>The description is longer than 160 characters</td><td>Warning</td></tr><tr><td>No main heading (an H1, or a hero)</td><td>Warning</td></tr><tr><td>More than one H1, anywhere on the page</td><td>Warning</td></tr><tr><td>Image blocks without alt text</td><td>Warning</td></tr><tr><td>Images with no <code>alt</code> attribute at all (<code>alt=""</code> is fine for decoration) [since 1.2.0]</td><td>Warning</td></tr><tr><td>Links that lead nowhere: to a page that isn\'t live, or was removed [since 1.2.0]</td><td>Warning</td></tr><tr><td>Links without text [since 1.2.0]</td><td>Warning</td></tr><tr><td>Another page has the same title</td><td>Warning</td></tr><tr><td>The title is shorter than 30 characters [since 1.2.0]</td><td>Suggestion</td></tr><tr><td>The description is shorter than 70 characters</td><td>Suggestion</td></tr><tr><td>A heading skips a level, such as H2 straight to H4 [since 1.2.0]</td><td>Suggestion</td></tr><tr><td>Links that go through a redirect, rather than to the page\'s own address [since 1.2.0]</td><td>Suggestion</td></tr><tr><td>Fewer than 300 words, on a page meant to be found [since 1.2.0]</td><td>Suggestion</td></tr><tr><td>No share image, on the page or for the site</td><td>Suggestion</td></tr><tr><td>Another page has the same description</td><td>Suggestion</td></tr><tr><td>Hidden from search engines, on purpose</td><td>Suggestion</td></tr></table><h2>The SEO report</h2><p>The report lists every live page with a problem or a warning, the pages needing most first. Suggestions are only shown on each page\'s own tab.</p>'),
            $this->screenshot('page-seo', 'A page\'s Search engines tab: its checks, a Google result preview, and the SEO title and description.'),
            $this->seeAlso(['pages' => 'The page form', 'site-search' => 'Search']),
        ]);
    }

    protected function blocksForPerformanceAndAccess(): array
    {
        return $this->doc('performance-and-access', [
            B::rich('<h2>The page cache</h2><p><strong>Settings › Performance › Page cache</strong> keeps rendered pages and serves them without running the page again.</p><ul><li><strong>Never cached</strong>: signed-in visitors, pages with a form, password-protected and members-only pages, previews, and addresses with query strings.</li><li><strong>Cleared</strong> on any change to what pages show, and no later than the next scheduled publish or unpublish.</li><li><strong>Responses</strong> say <code>X-Aurora-Cache: HIT</code> or <code>MISS</code>.</li></ul>'),
            $this->screenshot('page-cache', 'The page cache: on, how long pages are kept, and a button to clear it.'),
            B::rich('<h2>Restricting access</h2><ul><li><strong>A page</strong>: <em>Password protected</em> (visitors type a password, stored hashed) or <em>Only for signed-in people</em>.</li><li><strong>A whole site</strong>: Settings › Security › Website access limits the website to IP addresses or ranges ("Add my address"). People signed in to the admin always get in; everyone else sees a <em>site closed</em> page.</li><li><strong>Sign-in to the admin</strong>: Settings › Security › Sign-in can require two-factor sign-in or passkeys for everyone.</li><li><strong>Restyling</strong>: publish <code>aurora-views</code> to make the password, sign-in and site closed pages your own.</li></ul>'),
            $this->screenshot('security', 'Settings › Security: require two-factor sign-in or passkeys for everyone.'),
        ]);
    }

    protected function blocksForAdminThemes(): array
    {
        return $this->doc('admin-themes', [
            B::rich('<p>The admin\'s look is a theme package. Aurora ships <strong>Borealis</strong> (deep violet and night blue, the default) and <strong>Minimal</strong> (quiet, flat greys). The theme is picked for everyone under <strong>Settings › Appearance</strong>, and a theme can support light mode, dark mode or both. The mode switch only appears when it has both, and each theme shows its version [since 1.1.0].</p>'),
            $this->screenshot('themes', 'Settings › Appearance: the themes to choose from.'),
            B::rich('<p>A theme is only the CMS\'s look. Blocks on the editor\'s canvas are always in the website\'s own stylesheet, whichever theme is chosen.</p><h2>Writing a theme</h2><p>A theme is a package that registers itself:</p>'),
            B::code(<<<'PHP'
Themes::register(
    'acme',
    name: 'Acme',
    description: __('Our brand, in the admin.'),
    stylesheet: __DIR__.'/../resources/dist/theme.css',   // prebuilt
    preview: __DIR__.'/../resources/preview.webp',        // the card in Settings › Appearance
    package: 'acme/admin-theme',
    modes: ['light', 'dark'],
);
PHP, 'php'),
            B::rich('<ul><li><strong>Its picture</strong> is one for every mode, or one per mode (<code>preview: [\'light\' => …, \'dark\' => …]</code>): the theme picker shows the one for the mode the person is in.</li><li><strong>The CMS\'s components</strong> (canvas, inspector, media library and Settings screens) are styled once in <code>shipaurora/cms</code>\'s <code>components.css</code>, which every theme imports before its own styles.</li><li><strong>A theme</strong> styles Filament\'s <code>fi-*</code> classes and restyles <code>cms-*</code> hooks where it wants something different, with its own tokens. The Minimal theme (<code>shipaurora/minimal</code>) is a good one to copy.</li></ul>'),
        ]);
    }

    protected function blocksForMovingFromWordpress(): array
    {
        return $this->doc('moving-from-wordpress', [
            B::rich('<p>Export the site in WordPress (<em>Tools › Export</em>, <em>All content</em>), then import it under <strong>Settings › System › Import</strong>, or on the command line:</p>'),
            B::code('php artisan aurora:import-wordpress export.xml --site=main', 'bash'),
            B::rich('<ul><li>Pages come in as they are in WordPress, published or draft.</li><li>Blog posts come in too, under a Blog page (<code>--without-posts</code> leaves them out).</li><li>Images are downloaded into the media library (<code>--without-media</code> skips them).</li><li>Menus are rebuilt.</li></ul>'),
            $this->screenshot('import', 'Settings › System › Import: upload a WordPress export.'),
            $this->seeAlso(['moving-from-another-cms' => 'Moving from another CMS']),
        ]);
    }

    protected function blocksForMovingFromAnotherCms(): array
    {
        return $this->doc('moving-from-another-cms', [
            B::rich('<p>From WordPress, the importer does the work. From any other CMS, write an import: an Artisan command or a seeder that reads the old site and writes Aurora\'s models. This page shows each part. The tables behind them are in the <em>Data reference</em>.</p><h2>Pages and their blocks</h2><p>A page\'s blocks are one flat list for every language. Write them as a tree, and <code>BlockTreeBuilder::flatten()</code> makes the list, giving each block an id:</p>'),
            B::code(<<<'PHP'
use Aurora\Enums\PageStatus;
use Aurora\Models\Layout;
use Aurora\Models\Page;
use Aurora\Support\BlockTreeBuilder;

$blocks = BlockTreeBuilder::flatten([
    ['type' => 'heading', 'data' => ['text' => $old->title, 'level' => 'h1']],
    ['type' => 'band', 'data' => ['background' => 'muted'], 'children' => [
        'content' => [                                   // the band's slot
            ['type' => 'rich-text', 'data' => ['body' => $old->html]],
        ],
    ]],
]);

$page = Page::query()->create([
    'title' => $old->title,
    'slug' => $old->slug,
    'excerpt' => $old->summary,
    'layout_id' => Layout::query()->where('handle', 'standard')->value('id'),
    'status' => PageStatus::Published,
    'published_at' => $old->published_at,
    'blocks' => $blocks,
]);
PHP, 'php'),
            B::rich('<h2>Translations</h2><p>Title, address, summary and SEO fields are stored per language. Block text in other languages goes in <code>block_translations</code>, by block id and field, text only:</p>'),
            B::code(<<<'PHP'
$page->setTranslation('title', 'nb', $old->title_nb);
$page->setTranslation('slug', 'nb', $old->slug_nb);

$page->block_translations = [
    'nb' => [
        $blocks[0]['id'] => ['text' => $old->title_nb],
    ],
];

$page->save();
PHP, 'php'),
            B::rich('<h2>Layout fields [since 1.1.0]</h2><p>A layout\'s own fields are set with <code>settingsWithLayoutValues()</code>, naming the layout or its handle:</p>'),
            B::code("\$page->settings = \$page->settingsWithLayoutValues(['background' => \$asset->id], 'campaign');\n\$page->save();", 'php'),
            B::rich('<h2>Files</h2><p><code>AssetLibrary::fromFile()</code> adds a file to the media library. Give it an <code>id</code>, made from the old file\'s address, and running the import again finds the file rather than adding it twice [since 1.2.0]. Use the asset\'s id wherever blocks take a file or an image.</p>'),
            B::code(<<<'PHP'
use Aurora\Support\AssetLibrary;
use Ramsey\Uuid\Uuid;

$asset = AssetLibrary::fromFile($path, [
    'id' => Uuid::uuid5(Uuid::NAMESPACE_URL, $old->image_url)->toString(),
    'title' => $old->image_title,
    'alt' => $old->image_alt,
    'folder' => 'Imported/2026',
]);

['type' => 'image', 'data' => ['src' => $asset->id, 'alt' => $old->image_alt]];
PHP, 'php'),
            B::rich('<p>Files larger than the media library takes are refused: 10 MB for images and 50 MB for other files, unless <code>aurora.media.max_image_mb</code> and <code>max_file_mb</code> say otherwise [since 1.1.0]. Raise them for the import if you need to.</p><h2>Redirects and menus</h2>'),
            B::code(<<<'PHP'
use Aurora\Models\Menu;
use Aurora\Models\MenuItem;
use Aurora\Models\Redirect;

// The old address keeps working, and follows the page if its address changes.
Redirect::query()->create(['from_path' => '/about.html', 'page_id' => $page->id, 'status_code' => 301]);

$menu = Menu::query()->create(['name' => 'Main navigation', 'is_visible' => true]);
$menu->locations()->create(['location' => 'header']);

MenuItem::query()->create([
    'menu_id' => $menu->id,
    'linkable_type' => $page->getMorphClass(),
    'linkable_id' => $page->id,
    'order' => 1,
]);
PHP, 'php'),
            B::rich('<h2>Good to know</h2><ul><li><strong>Saves from code go straight on the page</strong>, live or not. Only the page form, the visual editor and AI tools keep a live page\'s changes waiting as unpublished changes.</li><li><strong>Several sites</strong>: import into another site with <code>CurrentSite::using($site, fn () =&gt; …)</code>.</li><li><strong>Search</strong>: rebuild the index once the import is done, with <code>php artisan aurora:reindex</code> [since 1.0.4].</li></ul>'),
            $this->seeAlso(['data-reference' => 'Data reference', 'moving-from-wordpress' => 'Moving from WordPress']),
        ]);
    }

    /* ── AI and integrations ──────────────────────────────────────────── */

    protected function blocksForAi(): array
    {
        return $this->doc('ai', [
            B::rich('<h2>The MCP server</h2><p>At <code>POST /mcp</code> (Streamable HTTP, OAuth with Laravel Passport).</p><ul><li><strong>Connecting</strong>: Claude Desktop and claude.ai add it as a <em>custom connector</em>. Claude Code, VS Code and Cursor sign in through the browser. Scripts use personal access tokens. <strong>AI › Connect AI apps</strong> has step-by-step setup with copy buttons, the apps and tokens you\'ve connected, and what your assistant can do.</li><li><strong>Tools</strong>: sites and identity (<code>whoami</code>, <code>list_sites</code>); pages (<code>list_pages</code>, <code>get_page</code>, <code>create_page</code>, <code>update_page</code>, <code>delete_page</code>, <code>publish_page</code>); blocks (<code>list_block_types</code>, <code>set_page_blocks</code>, <code>add_block</code>, <code>update_block</code>, <code>move_block</code>, <code>remove_block</code>); history (<code>list_revisions</code>, <code>restore_revision</code>); media (<code>list_images</code>, <code>upload_image_from_url</code>); menus (<code>get_menus</code>, <code>add_menu_item</code>, <code>update_menu_item</code>, <code>remove_menu_item</code>); and <code>list_forms</code>, <code>list_layouts</code>, <code>seo_report</code>. Layouts\' own fields are listed by <code>list_layouts</code>, read by <code>get_page</code> and set with <code>layout_settings</code> on <code>create_page</code> and <code>update_page</code>.</li><li><strong>Permissions</strong>: the AI acts as the person who connected it. Tools their role can\'t use aren\'t even listed. Publishing needs the publish permission. Changes to live pages wait as unpublished changes, and appear in the page history under the person\'s name.</li><li><strong>Restricted callbacks</strong>: OAuth clients may only redirect to Claude, localhost or the Claude, Cursor and VS Code app schemes.</li><li><strong>Development</strong>: expose the site (for example <code>herd share</code>) and set <code>MCP_PUBLIC_URL</code>. <code>php artisan mcp:start cms</code> runs a local stdio server as <code>MCP_USER</code>.</li></ul>'),
            $this->screenshot('connect-ai', 'AI › Connect AI apps: setup for each app, with copy buttons.'),
            B::rich('<h2>The assistant in the admin</h2><p>Set up under <strong>Settings › AI Assistant</strong>.</p><ul><li><strong>Providers</strong>: Anthropic (Claude), OpenAI, Google Gemini, Mistral, Groq, xAI, DeepSeek, or any OpenAI-compatible or Anthropic-compatible endpoint, including OpenRouter, Ollama and LM Studio.</li><li><strong>What it uses</strong>: the same tools as MCP, with the same permissions, logged as "Assistant".</li><li><strong>What it knows</strong>: the screen you\'re on, so "make the heading shorter" needs no page name.</li><li><strong>Attachments</strong>: images, PDFs, text and CSV. Uploaded images only join the media library when a page uses them.</li><li><strong>Conversations</strong> are kept and can be exported as Markdown, and open editors redraw when it changes the page.</li></ul><h2>Write with AI, translation and alt text</h2><ul><li><strong>Write with AI</strong> is a sparkle button on text fields. It writes from the rest of the form and the page, with an optional instruction, and for alt text it looks at the image.</li><li><strong>AI translation</strong> translates pages block by block, form fields and every label.</li><li><strong>Alt text, written automatically</strong>: switch on <em>Write alt text for images automatically</em> under Settings › AI Assistant, and the assistant describes each image added to the media library, in the website\'s language, on the queue. Switching it on also describes the images already there without alt text. Alt text someone wrote is never replaced.</li><li><strong>Alt text for selected images</strong> [since 1.2.3]: select images in the media library and choose <em>Write alt text with AI</em> from the bulk actions. Add an instruction if you like (where the photos were taken, say), and the assistant describes the ones without alt text, on the queue. This works whether or not alt text is written automatically.</li></ul><h2>The AI activity log</h2><p><strong>AI › Activity log</strong> records every tool call by an AI: the channel (an app over MCP, or the assistant), the tool, the person it acted for, the outcome and the arguments, with a link to the page it changed.</p>'),
            $this->seeAlso(['let-claude-draft-a-page' => 'Guide: let Claude draft a page', 'users-and-roles' => 'Roles and permissions']),
        ]);
    }

    protected function blocksForHeadlessApis(): array
    {
        return $this->doc('headless-apis', [
            B::rich('<p>Switch either API on under <strong>Settings › Integrations</strong>. Both are read-only and only show what visitors may see: live pages that aren\'t password-protected or members-only, files those pages use, and public settings.</p>'),
            $this->screenshot('api', 'Settings › Integrations › REST API: on, whether a key is needed, and how to call it.'),
            B::rich('<h2>Explore them [since 1.0.8]</h2><p>Each API\'s tab under <strong>Settings › Integrations</strong> links to an explorer in the admin, for the people who may change the settings. <strong>The API explorer</strong> (Swagger UI, at <code>/admin/api/rest</code>) documents every endpoint and lets you try it with one of your keys. Its OpenAPI document, at <code>/admin/api/rest/openapi.json</code>, is made from the API\'s own routes each time, so it\'s always current; tools can generate a client from it. <strong>The GraphQL playground</strong> (GraphiQL, at <code>/admin/api/graphql</code>) reads the schema from the API itself.</p>'),
            B::rich('<h2>REST</h2><p>At <code>/api/aurora/v1</code>. Add <code>?locale=nb</code> for another language.</p><table><tr><th>Endpoint</th><th>Returns</th><th>Scope</th></tr><tr><td><code>GET /pages</code> (<code>?page=2</code>, <code>?parent={id}</code>)</td><td>Live pages in brief, 50 per page</td><td><code>pages.list</code></td></tr><tr><td><code>GET /pages/{id}</code></td><td>A page with its structured blocks and SEO</td><td><code>pages.read</code></td></tr><tr><td><code>GET /page?path=/about</code></td><td>The page at an address (<code>/</code> is the homepage)</td><td><code>pages.read</code></td></tr><tr><td><code>GET /search?q=pricing</code></td><td>Search results with a snippet and score</td><td><code>search</code></td></tr><tr><td><code>GET /media</code>, <code>GET /media/{id}</code></td><td>Files the pages use, with sizes and <code>srcset</code></td><td><code>media.read</code></td></tr><tr><td><code>GET /menus/{location}</code></td><td>A menu and its items</td><td><code>menus.read</code></td></tr><tr><td><code>GET /settings</code></td><td>The site\'s public settings</td><td><code>settings.read</code></td></tr></table>'),
            B::code(<<<'BASH'
curl https://example.com/api/aurora/v1/page?path=/docs \
     -H "Authorization: Bearer aurora_…"
BASH, 'bash'),
            B::rich('<h2>GraphQL</h2><p>At <code>/api/aurora/graphql</code> (POST <code>{"query": "…"}</code>). It offers <code>pages</code>, <code>page(id:, path:)</code>, <code>search(query:)</code>, <code>menu(location:)</code>, <code>settings</code>, <code>media(id:)</code> and <code>mediaList</code>, and tools like GraphiQL, Altair and Postman can read its schema. Queries nested more than 10 levels deep are turned away.</p>'),
            B::code(<<<'GRAPHQL'
{
  page(path: "/") {
    title
    seo { title description image { url width height } }
    blocks
  }
  menu(location: "header") { items { title url children { title url } } }
}
GRAPHQL, 'graphql'),
            B::rich('<h2>Structured blocks</h2><p>IDs in block data come out as what they point at: images and files with their URLs, sizes, <code>srcset</code>, alt text and focal point; page links with the page\'s title, path and URL; forms with their fields; rich text as cleaned HTML.</p><h2>API keys</h2><ul><li>Either API can require a key; the same keys work for both. Apps send it as <code>Authorization: Bearer &lt;key&gt;</code>, never in the address.</li><li>Each key is named after what uses it, shown once, stored hashed, and revocable: under Settings › Security › API keys, or <em>Manage keys</em> on each API\'s tab.</li><li>Each key has <strong>scopes</strong>: list pages, read page content, search, read files, read menus and read settings. A request outside them is refused.</li></ul>'),
            $this->screenshot('api-keys', 'API keys: each named, with its scopes and when it was last used.'),
        ]);
    }

    protected function blocksForWebhooks(): array
    {
        return $this->doc('webhooks', [
            B::rich('<p><strong>Settings › Integrations › Webhooks</strong> sends a signed POST with JSON when content changes.</p><ul><li><strong>Events</strong>: <code>page.saved</code>, <code>page.published</code>, <code>page.unpublished</code>, <code>page.deleted</code> and <code>form.submitted</code>.</li><li><strong>Signing</strong>: each request carries <code>X-Aurora-Signature</code>, an HMAC-SHA256 of the body with the webhook\'s secret.</li><li><strong>Delivery and checking</strong>: requests are sent from the queue and tried again when the receiver isn\'t reachable. <em>Send a test</em> and the last response help when setting one up.</li></ul>'),
            $this->screenshot('webhooks', 'Webhooks: where to send content events, with a signing secret.'),
            B::rich('<h2>Laravel events</h2><p>The same moments are Laravel events your app can listen to: <code>Aurora\Events\PageSaved</code>, <code>PagePublished</code>, <code>PageUnpublished</code>, <code>PageDeleted</code> and <code>FormSubmitted</code>.</p>'),
            B::code(<<<'PHP'
use Aurora\Events\PagePublished;
use Illuminate\Support\Facades\Event;

Event::listen(function (PagePublished $event) {
    // Purge a CDN, rebuild a static app, tell the team…
});
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
        ]);
    }

    /* ── Reference ────────────────────────────────────────────────────── */

    protected function blocksForConfiguration(): array
    {
        return $this->doc('configuration', [
            B::rich('<p>Publish the config with <code>php artisan vendor:publish --tag=aurora-config</code> (<code>config/aurora.php</code>).</p><table><tr><th>Key</th><th>Env</th><th>Default</th><th>What it does</th></tr><tr><td><code>name</code></td><td><code>AURORA_NAME</code></td><td>Aurora CMS</td><td>The CMS\'s name in the admin</td></tr><tr><td><code>user_model</code></td><td></td><td><code>Aurora\Models\User</code></td><td>The user model</td></tr><tr><td><code>demo_login</code></td><td><code>AURORA_DEMO_LOGIN</code></td><td><code>false</code></td><td>One-click sign-in as the demo accounts. Never in production</td></tr><tr><td><code>theme</code></td><td><code>AURORA_THEME</code></td><td>Borealis</td><td>The admin theme until one is chosen in Settings › Appearance</td></tr><tr><td><code>website_css</code></td><td><code>AURORA_WEBSITE_CSS</code></td><td><code>resources/css/app.css</code></td><td>The website\'s stylesheet for the canvas</td></tr><tr><td><code>block_path</code></td><td></td><td><code>app/Blocks</code></td><td>Where the site\'s blocks are found</td></tr><tr><td><code>block_namespace</code></td><td></td><td>from the path</td><td>Their namespace</td></tr><tr><td><code>layout_path</code> [since 1.2.2]</td><td></td><td><code>app/Layouts</code></td><td>Where the site\'s layouts defined in code are found; null to register them by hand</td></tr><tr><td><code>layout_namespace</code> [since 1.2.2]</td><td></td><td>from the path</td><td>Their namespace</td></tr><tr><td><code>routes</code></td><td><code>AURORA_ROUTES</code></td><td><code>true</code></td><td>Whether Aurora serves the website\'s pages</td></tr><tr><td><code>translations_path</code></td><td><code>AURORA_TRANSLATIONS_PATH</code></td><td></td><td>Where <code>aurora:sync-translations</code> adds labels and <code>aurora:export-translations</code> writes</td></tr><tr><td><code>media.max_image_mb</code> [since 1.1.0]</td><td><code>AURORA_MAX_IMAGE_MB</code></td><td>10</td><td>The largest image the media library takes, in MB</td></tr><tr><td><code>media.max_file_mb</code> [since 1.1.0]</td><td><code>AURORA_MAX_FILE_MB</code></td><td>50</td><td>The largest other file (videos, PDFs), in MB</td></tr><tr><td><code>mcp.local_user</code> [since 1.0.10]</td><td><code>MCP_USER</code></td><td></td><td>Who the local stdio MCP server acts as</td></tr><tr><td><code>mcp.public_url</code> [since 1.0.10]</td><td><code>MCP_PUBLIC_URL</code></td><td></td><td>The public address AI connectors reach in development</td></tr></table><p>PHP\'s <code>upload_max_filesize</code> and <code>post_max_size</code>, and the web server\'s own limit (<code>client_max_body_size</code> on nginx), must allow as much as the media limits, or larger uploads fail before they reach Aurora.</p><h2>Publish tags</h2><p><code>aurora-config</code>, <code>aurora-layouts</code>, <code>aurora-views</code> (the password, sign-in and site closed pages) and <code>aurora-stubs</code> (the <code>make-block</code> stubs).</p><h2>Set elsewhere</h2><table><tr><th>What</th><th>Where</th></tr><tr><td>The media disk</td><td>Spatie Media Library\'s config: <code>media-library.disk_name</code></td></tr><tr><td>Languages, the page cache, website access</td><td>Settings, per site: Languages &amp; sites, Performance, Security</td></tr><tr><td>The website\'s own admin screens</td><td><code>AdminPanel</code> [since 1.2.0], from a service provider (<em>Your own screens and models</em>)</td></tr><tr><td>The page model and the table names</td><td>Fixed: <code>Aurora\Models\Page</code>, and the tables in the <em>Data reference</em></td></tr><tr><td>The admin\'s address</td><td>Fixed: always <code>/admin</code></td></tr></table>'),
            $this->note('Don\'t set aurora.path', 'The admin is always at /admin. The aurora.path key only changes where a passkey sign-in redirects to, so setting it breaks that redirect.', 'warning'),
        ]);
    }

    protected function blocksForBladeHelpers(): array
    {
        return $this->doc('blade-helpers', [
            B::rich('<p>Every helper, directive and component Aurora gives block views and layouts. They\'re global, so views need no <code>@php use …</code> imports. How to use them is under <em>Block views and helpers</em>.</p><h2>Helpers</h2><table><tr><th>Helper</th><th>Returns</th></tr><tr><td><code>inline_text($text)</code></td><td>Text, escaped, with <code>*highlighted*</code> words</td></tr><tr><td><code>plain_text($text)</code></td><td>Text, escaped, line breaks kept</td></tr><tr><td><code>rich_text($html)</code></td><td>Rich text from a RichEditor, cleaned</td></tr><tr><td><code>shows_field($field, $value)</code></td><td>Whether to draw an optional field: when it\'s filled, or when the canvas is editing</td></tr><tr><td><code>link_href($data, $prefix = \'link\')</code></td><td>The address of a <code>Fields::link()</code> group: a page, a file or a URL</td></tr><tr><td><code>link_target($data, $prefix = \'link\')</code></td><td><code>_blank</code> when the link opens in a new tab, otherwise null</td></tr><tr><td><code>is_editing()</code></td><td>Whether the block is drawn on the canvas</td></tr><tr><td><code>block_slot($name)</code></td><td>A container block\'s slot, with the blocks in it</td></tr><tr><td><code>current_page()</code> [since 1.2.0]</td><td>The page being drawn, or null in the palette\'s preview of a block</td></tr><tr><td><code>block_depth()</code> [since 1.2.0]</td><td>0 straight on the page, 1 inside one block, and so on</td></tr><tr><td><code>is_nested_block()</code> [since 1.2.0]</td><td>Whether the block is inside another block</td></tr></table><h2>Directives</h2><table><tr><th>Directive</th><th>Does</th></tr><tr><td><code>@editable(\'field\')</code></td><td>Makes the element editable on the canvas, for a field in <code>editables()</code>; nothing elsewhere</td></tr><tr><td><code>@editableItem(\'items\', $index, \'field\')</code> [since 1.2.1]</td><td>The same for a field inside a repeater\'s item, declared in <code>itemEditables()</code></td></tr><tr><td><code>@inlineText(…)</code>, <code>@plainText(…)</code>, <code>@richText(…)</code></td><td>Print what the helper of the same name returns</td></tr><tr><td><code>@linkHref($data)</code>, <code>@linkTarget($data)</code></td><td>Print a link\'s address and target</td></tr><tr><td><code>@blockSlot(\'content\')</code></td><td>Prints a slot (not <code>@slot</code>: Blade\'s components use it)</td></tr><tr><td><code>@blockDepth</code> [since 1.2.0]</td><td>Prints the block\'s depth</td></tr><tr><td><code>@editing … @else … @endediting</code></td><td>Only on the canvas, or only elsewhere</td></tr><tr><td><code>@shows(\'body\', $data[\'body\'] ?? null) … @endshows</code></td><td>Only when the field should be drawn</td></tr><tr><td><code>@nested … @endnested</code> [since 1.2.0]</td><td>Only inside another block</td></tr></table><h2>Components</h2><table><tr><th>Component</th><th>Draws</th></tr><tr><td><code>&lt;x-aurora::blocks :page="$page" /&gt;</code></td><td>A page\'s blocks, in the language being shown. Attributes go on the element around them; <code>:blocks="$blocks"</code> draws a list of blocks instead [since 1.2.0]</td></tr><tr><td><code>&lt;x-aurora::media :value="…" size="md" /&gt;</code></td><td>A media library image, responsive (<code>sizes</code>), with the block\'s crop and the focal point</td></tr><tr><td><code>&lt;x-aurora::layout :title="…"&gt;…&lt;/x-aurora::layout&gt;</code> [since 1.2.2]</td><td>A website route\'s own content in one of the site\'s layouts, where a page\'s blocks would be. Also <code>description</code>, <code>layout</code>, <code>:settings</code> and <code>noindex</code> (see <em>Routing</em>)</td></tr></table>'),
            $this->seeAlso(['block-views' => 'Block views and helpers', 'creating-blocks' => 'Creating blocks']),
        ]);
    }

    protected function blocksForRouting(): array
    {
        return $this->doc('routing', [
            B::rich('<h2>How an address finds its page</h2><ol><li><strong>The app\'s own routes</strong>, in <code>routes/web.php</code>, load first and always win.</li><li><strong>Aurora\'s page routes</strong> come after: <code>/</code> (the homepage), <code>sitemap.xml</code>, <code>robots.txt</code>, signed <code>preview/{page}</code> links, then a catch-all for every other address. The catch-all leaves alone addresses starting with <code>admin</code>, <code>livewire</code>, <code>filament</code>, <code>oauth</code>, <code>mcp</code>, <code>api</code>, <code>storage</code> and <code>up</code>.</li><li><strong>An address that isn\'t a page</strong> goes to the stored redirects, then answers 404.</li></ol><p>Files (<code>/files/{id}</code>) and forms (<code>/forms/{form}</code>) have routes of their own. Set <code>aurora.routes</code> to false to turn the page routes off and route pages yourself with <code>Aurora\Http\Controllers\PageController</code>.</p><h2>Routes of your own</h2><p>Put the website\'s own routes in <code>routes/web.php</code>. <code>Aurora\Http\Middleware\UseSiteLocale</code> runs on every web route: an address starting with a language\'s prefix sets that language (<code>app()-&gt;getLocale()</code> and <code>ContentLocale::current()</code>). So a route in more than one language is defined under each prefix, next to the default one:</p>'),
            B::code(<<<'PHP'
use App\Http\Controllers\ShowEvents;

Route::get('/events', ShowEvents::class)->name('events');
Route::get('/no/events', ShowEvents::class)->name('events.nb');   // /no is Norwegian's prefix
PHP, 'php', 'routes/web.php'),
            B::rich('<p><code>ContentLocale::prefix($code)</code> gives a language\'s prefix (empty for the default language), and <code>Menus::localized([\'/events\'])</code> [since 1.2.0] makes menu links to <code>/events</code> go to the language being shown.</p><h2>The sitemap</h2><p><code>Sitemap::add()</code> [since 1.2.0] adds the website\'s own addresses to <code>sitemap.xml</code>, each a URL or an array:</p>'),
            B::code(<<<'PHP'
use Aurora\Support\Sitemap;

Sitemap::add(fn (): iterable => Event::query()->live()->get()->map(fn (Event $event): array => [
    'url' => route('events.show', $event),
    'updated_at' => $event->updated_at,
    'priority' => 0.6,
]));
PHP, 'php', 'app/Providers/AppServiceProvider.php'),
            B::rich('<h2>The site\'s layout around your own routes [since 1.2.2]</h2><p>A route of the website\'s own gets the site\'s head, header, menus and footer from one of its layouts, the same as a page. Wrap the view\'s content in <code>&lt;x-aurora::layout&gt;</code>, and it stands where a page\'s blocks would:</p>'),
            B::code(<<<'BLADE'
<x-aurora::layout :title="__('Events')" description="What's on this spring.">
    <section class="mx-auto max-w-5xl px-6 py-16">
        <h1>{{ __('Events') }}</h1>
        @foreach ($events as $event)
            <x-event-card :event="$event" />
        @endforeach
    </section>
</x-aurora::layout>
BLADE, 'blade', 'resources/views/events/index.blade.php'),
            B::rich('<table><tr><th>Attribute</th><th>What it does</th></tr><tr><td><code>title</code></td><td>The page\'s title, in the browser tab and for search engines. Required</td></tr><tr><td><code>description</code></td><td>The meta description</td></tr><tr><td><code>layout</code></td><td>The handle of the layout to use, such as <code>landing</code>; the default layout otherwise</td></tr><tr><td><code>:settings</code></td><td>The layout\'s own fields, as a page would have them (<code>$settings</code> in its view)</td></tr><tr><td><code>noindex</code></td><td>Keeps search engines away</td></tr></table><p>The layout gets a stand-in page with that title and description and the request\'s address: in each language, the same address under that language\'s prefix, so the layout\'s language switcher works. <code>current_page()</code> inside answers the stand-in, as on a page. It\'s only drawn, never saved.</p><p>From a controller, <code>Layouts::wrap()</code> returns the same HTML:</p>'),
            B::code(<<<'PHP'
use Aurora\Support\Layouts;

return response(Layouts::wrap(
    view('events.list', ['events' => $events]),   // anything Htmlable
    title: __('Events'),
    description: 'What\'s on this spring.',
    layout: 'landing',
));
PHP, 'php', 'app/Http/Controllers/ShowEvents.php'),
            $this->seeAlso(['layouts' => 'Layouts and the website', 'languages' => 'Languages', 'website-screens' => 'Your own screens and models']),
        ]);
    }

    protected function blocksForDataReference(): array
    {
        return $this->doc('data-reference', [
            B::rich('<p>The tables Aurora keeps content in, for imports, reports and queries. Use the models (<code>Aurora\Models\…</code>) to write: they keep the history, search index and caches up to date. Ids are UUIDs, and the fields marked <em>per language</em> are JSON keyed by language code (<code>{"en": "About us", "nb": "Om oss"}</code>).</p><h2>pages</h2><table><tr><th>Column</th><th>Holds</th></tr><tr><td><code>title</code>, <code>slug</code>, <code>excerpt</code>, <code>meta_title</code>, <code>meta_description</code></td><td>Per language</td></tr><tr><td><code>blocks</code></td><td>One flat list for every language, each block <code>{id, type, data, parent, slot, settings}</code></td></tr><tr><td><code>block_translations</code></td><td>Block text in other languages, text only: <code>{"nb": {"&lt;block id&gt;": {"&lt;field&gt;": "…"}}}</code></td></tr><tr><td><code>settings</code></td><td>The layouts\' own fields, by view: <code>{"layouts": {"&lt;view&gt;": {…}}}</code></td></tr><tr><td><code>locales</code></td><td>The languages the page is written in</td></tr><tr><td><code>status</code>, <code>published_at</code>, <code>unpublish_at</code></td><td><code>draft</code>, <code>review</code> or <code>published</code>, and the dates that schedule it</td></tr><tr><td><code>layout_id</code>, <code>parent_id</code>, <code>site_id</code>, <code>is_homepage</code></td><td>Its layout, parent page, site, and whether it\'s the homepage</td></tr><tr><td><code>og_image</code>, <code>noindex</code>, <code>exclude_from_search</code></td><td>The share image (an asset id), and whether it\'s hidden from search engines and from site search</td></tr><tr><td><code>password</code>, <code>members_only</code></td><td>A password (hashed), or signed-in visitors only</td></tr><tr><td><code>working_copy</code>, <code>working_copy_by_id</code>, <code>working_copy_at</code></td><td>Unpublished changes to a live page, and who saved them when</td></tr><tr><td><code>author_id</code>, <code>last_edited_by_id</code></td><td>Who wrote it, and who changed it last</td></tr></table><h2>The other tables</h2><table><tr><th>Table</th><th>Columns</th></tr><tr><td><code>layouts</code></td><td><code>name</code>, <code>handle</code>, <code>description</code>, <code>icon</code>, <code>view</code>, <code>is_default</code>, <code>sort</code>, and <code>class</code> for a layout defined in code</td></tr><tr><td><code>assets</code></td><td><code>title</code>, <code>alt</code> and <code>caption</code> (per language), <code>credit</code>, <code>focal_point</code>, <code>folder_id</code>, <code>mime_type</code>, <code>disk</code>, <code>access</code>; folders in <code>asset_folders</code>, where each file is used in <code>asset_usages</code></td></tr><tr><td><code>redirects</code></td><td><code>from_path</code>, and where it goes: <code>page_id</code>, <code>asset_id</code> or <code>to_url</code>; <code>status_code</code>, <code>hits</code>, <code>last_hit_at</code>, <code>site_id</code></td></tr><tr><td><code>menus</code></td><td><code>name</code>, <code>is_visible</code>, <code>site_id</code>; where each is shown in <code>menu_locations</code> (<code>location</code>)</td></tr><tr><td><code>menu_items</code></td><td><code>menu_id</code>, <code>parent_id</code>, <code>title</code> (per language), <code>linkable_type</code> and <code>linkable_id</code> (a page) or <code>url</code>, <code>icon</code>, <code>classes</code>, <code>rel</code>, <code>target</code>, <code>order</code></td></tr><tr><td><code>settings</code></td><td><code>key</code> and <code>value</code> (JSON); read them with <code>Setting::get()</code></td></tr><tr><td><code>language_lines</code></td><td>The admin\'s labels: <code>group</code>, <code>key</code>, <code>text</code> (per language) and <code>imported</code></td></tr></table>'),
            $this->seeAlso(['moving-from-another-cms' => 'Moving from another CMS']),
        ]);
    }

    protected function blocksForArtisanCommands(): array
    {
        return $this->doc('artisan-commands', [
            B::rich('<table><tr><th>Command</th><th>What it does</th></tr><tr><td><code>aurora:install</code></td><td>Sets up the CMS: key, database, required content, labels, admin account. Options: <code>--fresh</code>, <code>--force</code>, <code>--seed</code>, <code>--site-name</code>, <code>--language</code>, <code>--languages</code>, <code>--name</code>, <code>--email</code>, <code>--password</code>, <code>--build</code> [since 1.0.6] (installs and builds the website\'s front end without asking), <code>--interactive-only</code> [since 1.0.1] (the starter app\'s <code>create-project</code> runs it this way: without a terminal to answer in, it says how to finish)</td></tr><tr><td><code>make:aurora-layout {name}</code> [since 1.2.2]</td><td>Creates a page layout in code: its class in <code>app/Layouts</code> and its view in <code>resources/views/layouts</code>, started from the standard layout. Editors can pick it straight away. Options: <code>--description</code>, <code>--icon</code>, <code>--force</code></td></tr><tr><td><code>aurora:reindex</code> [since 1.0.4]</td><td>Rebuilds the search index from every page\'s content. Options: <code>--site</code> (a site\'s handle; every site otherwise)</td></tr><tr><td><code>make:aurora-user</code></td><td>Creates a user and gives them a role, asking the same questions as <code>make:filament-user</code> plus the role. Options: <code>--name</code>, <code>--email</code>, <code>--password</code>, <code>--role</code> (a role\'s name, e.g. <code>Editor</code>)</td></tr><tr><td><code>aurora:make-block {name}</code></td><td>Creates a block class and its view. Options: <code>--container</code>, <code>--category</code>, <code>--icon</code>, <code>--force</code></td></tr><tr><td><code>aurora:import-wordpress {file}</code></td><td>Imports a WordPress export. Options: <code>--site</code>, <code>--without-posts</code>, <code>--without-media</code></td></tr><tr><td><code>aurora:sync-translations</code></td><td>Adds new labels from the code to the default lang file, then imports the lang files</td></tr><tr><td><code>aurora:import-translations</code></td><td>Imports labels from the lang files, keeping edits made in the admin</td></tr><tr><td><code>aurora:export-translations</code></td><td>Writes labels edited in the admin back to lang files</td></tr><tr><td><code>mcp:start cms</code></td><td>Runs the MCP server over stdio, for local development</td></tr></table>'),
        ]);
    }

    protected function blocksForPackagesAndDevelopment(): array
    {
        return $this->doc('packages-and-development', [
            B::rich('<h2>Packages</h2><table><tr><th>Package</th><th>Namespace</th><th>Contents</th></tr><tr><td><code>shipaurora/aurora</code></td><td><code>App\</code></td><td>The application template a new website starts from (<code>composer create-project</code>)</td></tr><tr><td><code>shipaurora/cms</code></td><td><code>Aurora\</code></td><td>The CMS: models, Filament admin, visual editor extensions, MCP server, REST and GraphQL APIs, migrations, views, translations (English and Norwegian), admin scripts and the shared component styles</td></tr><tr><td><code>shipaurora/borealis</code></td><td><code>Aurora\Borealis\</code></td><td>Borealis, the default admin theme, prebuilt</td></tr><tr><td><code>shipaurora/minimal</code></td><td><code>Aurora\Minimal\</code></td><td>The Minimal admin theme, prebuilt</td></tr></table><h2>Working with AI coding agents</h2><p>Aurora ships guidelines and a skill for Laravel Boost, so coding agents know how to build blocks, layouts and settings in an Aurora project. Run <code>php artisan boost:install</code> and they\'re picked up with the rest.</p><h2>Development</h2>'),
            B::code(<<<'BASH'
composer dev                         # server, queue, logs and Vite
npm run build                        # the website's assets
php artisan test --parallel          # the test suite, in parallel (ParaTest)
vendor/bin/pint --dirty              # code style
BASH, 'bash'),
            B::rich('<p>The test suite (PHPUnit) covers permissions on every surface, the publishing workflow and unpublished changes, the visual editor (every block can be inserted, edited and saved), the public website, the APIs and their scopes, MCP tools, media, forms, translations and settings. Tests never reach the network: a request that isn\'t faked fails.</p><h2>License</h2><p>Aurora CMS is open-source software licensed under the MIT license. Copyright © 2026 Thomas Alrek.</p>'),
        ]);
    }
}
