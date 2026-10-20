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
            'site-search' => ['Search', 'Search live pages with Laravel Scout, per site and language.'],
        ]],
        'running-a-site' => ['Running a site', 'People, languages, sites, settings and speed.', [
            'users-and-roles' => ['Users, roles and permissions', 'Invitations, sign-in with passkeys, roles in plain language and the activity log.'],
            'languages' => ['Languages and translations', 'Content in every language, and every label in the admin.'],
            'several-websites' => ['Several websites', 'Run more than one website, each with its own domain, from one admin.'],
            'settings' => ['Settings', 'Every page and tab in Settings, and what each one holds.'],
            'performance-and-access' => ['Page cache and access', 'Serve pages ready-made, and decide who may see them.'],
            'admin-themes' => ['Admin themes', 'Choose the admin\'s look, or write a theme of your own.'],
            'moving-from-wordpress' => ['Moving from WordPress', 'Bring pages, posts, images and menus over from a WordPress export.'],
        ]],
        'ai-and-integrations' => ['AI and integrations', 'AI assistants, the REST and GraphQL APIs, and webhooks.', [
            'ai' => ['AI: MCP, the assistant and Write with AI', 'Connect Claude and other apps, chat with the assistant, and let AI write and translate.'],
            'headless-apis' => ['REST and GraphQL APIs', 'Read pages, menus, media and settings from apps, with API keys and scopes.'],
            'webhooks' => ['Webhooks and events', 'Tell other systems when content changes, or listen in your own app.'],
        ]],
        'reference' => ['Reference', 'Configuration, commands and packages.', [
            'configuration' => ['Configuration', 'Every config key, environment variable and publish tag.'],
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
            B::rich('<h2>Requirements</h2><ul><li>PHP 8.3 or newer, with the GD or Imagick extension</li><li>Composer 2 and Node.js 20+</li><li>A database: SQLite, MySQL/MariaDB or PostgreSQL. MySQL or MariaDB gives the best search, with full-text relevance.</li><li>A queue worker in production, for notifications, image formats and webhooks</li><li>HTTPS in production, which passkeys require</li></ul><h2>Start a new project</h2><p>New websites start from the <strong>application template</strong>. It\'s a complete Laravel application with the CMS already wired in, the page layouts published and ready to make your own, <code>app/Blocks</code> as the place for your own blocks, and the build set up for the website and the admin. The CMS itself comes in through Composer, so you update it with <code>composer update</code> while your website\'s code stays yours.</p>'),
            B::code(<<<'BASH'
composer create-project shipaurora/aurora my-site   # ends by running aurora:install
cd my-site && npm install && npm run build
BASH, 'bash'),
            B::rich('<p><code>aurora:install</code> walks you through setup:</p><ul><li>creates <code>.env</code> if needed and the application key;</li><li>runs the migrations, or with <code>--fresh</code> starts over;</li><li>publishes the admin\'s prebuilt theme and scripts, and the page layouts;</li><li>seeds what the CMS needs (roles, permissions, the site and its languages); a website\'s own content comes from its own seeders;</li><li>indexes pages for search, syncs labels from the lang files, creates the OAuth keys for AI apps and links public storage;</li><li>creates your admin account, a Super Admin;</li><li>runs the website\'s own seeders (<code>database/seeders/DatabaseSeeder</code>, its first content, such as the homepage) when you say yes, or with <code>--seed</code>.</li></ul><p>It can also run unattended:</p>'),
            B::code(<<<'BASH'
php artisan aurora:install \
    --site-name="Acme" --language=en --languages="nb:no,sv" \
    --name="Ada Admin" --email=ada@example.com --password=secret
BASH, 'bash'),
            B::rich('<p>Serve it the way you usually do. With Laravel Herd it\'s <code>http://my-site.test</code>; otherwise use <code>php artisan serve</code>. Open <code>/admin</code> to sign in.</p>'),
            B::rich('<h2>Adding Aurora to an existing Laravel application</h2><p>Starting from the template is the recommended way. Aurora can also be added to an existing Laravel 13 application; you then set up yourself what the template sets up for you: the layouts and the site\'s CSS.</p>'),
            B::code("composer require shipaurora/cms\nphp artisan aurora:install", 'bash'),
            B::rich('<p>Aurora registers its own service providers and its Filament panel at <code>/admin</code>, and brings its default admin theme with it. Then add your own blocks. Optionally, <code>shipaurora/minimal-theme</code> adds a second admin theme to choose from.</p>'),
            $this->seeAlso(['quick-setup' => 'Quick setup', 'creating-blocks' => 'Creating blocks']),
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
            B::rich('<p>The heart of the CMS. The sidebar badge counts pages waiting for review.</p><h2>The list</h2><ul><li><strong>Tabs</strong>: All pages, Published, Scheduled, Review and Drafts, plus <em>Ended</em> (the unpublish date has passed) and <em>Changes waiting</em> (unpublished changes) when they have pages.</li><li><strong>Each row</strong> shows the full address on the site\'s domain; icons for the homepage, password protection and someone else editing; the status; and the layout, block count, author and last edit.</li><li><strong>Search and filters</strong>: by title and address; filter by status, layout, author and trashed.</li><li><strong>New page</strong> asks for a title, then opens the form with the address filled in. <em>Start from</em> copies another page\'s blocks as a template.</li><li><strong>Row actions</strong>: Design (visual editor), Publish, Settings, View or Preview, Create subpage, Duplicate, Delete and Restore.</li><li><strong>Bulk actions</strong>: Publish, Move to drafts, Reindex, Delete and Restore, each checked page by page against permissions.</li></ul>'),
            $this->screenshot('pages', 'The pages list, with its tabs, statuses and the last edit of every page.'),
            B::rich('<h2>The page form</h2><ul><li><strong>Page</strong>: title, parent page, address (with the domain, language prefix and parent path shown; reserved and duplicate addresses refused) and summary.</li><li><strong>Content</strong>: a preview of the page in its layout, with <em>Open visual editor</em>.</li><li><strong>Search engines</strong>: SEO checks (title and description length, a missing description, duplicates across pages, a missing or doubled main heading, images without alt text, a missing share image), a Google result preview, the SEO title and meta description, and <em>Hide from search engines</em>.</li><li><strong>Social sharing</strong>: the share image, cropped to 1200×630, and a social card preview.</li><li><strong>Publishing</strong>: status, publish and unpublish dates, <em>Use as homepage</em>, <em>Only for signed-in people</em> and <em>Password protected</em>.</li><li><strong>Languages</strong>, <strong>Layout</strong> (cards with thumbnails) and <strong>Details</strong>: the author, the last editor, revisions and search indexing.</li></ul>'),
            $this->screenshot('page-seo', 'Search engines: SEO checks, a Google result preview, and the SEO title and description with Write with AI.'),
            B::rich('<h2>Around the form</h2><ul><li><strong>Language switcher</strong>: edit the page\'s text in each language.</li><li><strong>Preview</strong>: a signed link that shows a draft, a scheduled page or unpublished changes inside the real site.</li><li><strong>Edit locks</strong>: opening a page takes a lock, renewed every 20 seconds and dropped after 60 seconds without renewal. Others see who is editing and can <em>Take over</em>; saves from anyone else are refused while the lock is held.</li><li><strong>Changing the address</strong> of a live page asks whether to keep the old address working, then creates 301 redirects in every language. Subpages follow.</li><li><strong>History</strong>: every revision with its source (Editor, Visual editor, MCP, Assistant, Restore or System), the person and a summary, a preview of any revision, and one-click restore.</li><li><strong>Duplicate</strong>: a draft copy with the same content, layout and translations.</li></ul>'),
            $this->screenshot('history', 'A page\'s history: who changed it, from where, and a restore button for every revision.'),
            $this->seeAlso(['publishing' => 'Publishing workflow', 'visual-editor' => 'The visual editor']),
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
            B::rich('<h2>The panels</h2><ul><li><strong>Blocks</strong>: a palette of blocks, grouped by category and searchable, each with a picture of what it looks like. Click a block to add it, or drag it onto the canvas, into a band, a column or a grid.</li><li><strong>Structure</strong>: an outline of the page to select, move and nest blocks.</li><li><strong>Inspector</strong>: the selected block\'s fields, which update the canvas as they change. Every block also has design options: padding, background, width and alignment.</li></ul><h2>On the canvas</h2><ul><li><strong>Inline editing</strong>: headings, paragraphs, button labels, and items inside lists, such as an FAQ question or a pricing feature. <code>*Asterisks*</code> highlight words in headings.</li><li><strong>Images</strong>: clicking an empty image opens the media picker. Each use of an image can be cropped, rotated, flipped and adjusted without changing the original.</li><li><strong>Embeds</strong>: drop a YouTube, Vimeo, map or other allowed link onto an Embed block.</li><li><strong>Editing tools</strong>: desktop, tablet and mobile widths; undo and redo with keyboard shortcuts; copy and paste blocks and styles; resize blocks and columns; a right-click menu.</li><li><strong>Preview</strong> renders the unsaved page inside the real website: header, menus, footer and CSS.</li></ul>'),
            $this->screenshot('editor-mobile', 'The same page at mobile width, with the structure outline open.'),
            B::rich('<h2>Saving</h2><ul><li>The status shows <em>Unsaved changes</em>, <em>Saved</em>, or <em>Saved, not published</em> when the page has unpublished changes, with a <strong>Publish</strong> button.</li><li>Saves are recorded as "Visual editor" revisions and respect edit locks.</li><li>Leaving with unsaved changes offers <em>Discard</em> or <em>Save and leave</em>.</li></ul><h2>Translating</h2><ul><li>Open the language menu, or add <code>?language=nb</code>. All languages share one block structure, and in another language only the text can be edited.</li><li>Untranslated text is highlighted.</li><li><em>Translate with AI</em> fills in what\'s missing, or translates everything, including the title, summary and SEO fields.</li></ul><h2>And more</h2><ul><li><strong>Shortcodes</strong> (<code>[site_name]</code>, <code>[page_count]</code>) and <strong>code blocks</strong>, which are only for people with the <em>code blocks</em> permission.</li><li><strong>The AI assistant</strong> can change the page while it\'s open; the canvas redraws without reloading.</li></ul>'),
            $this->seeAlso(['canvas-styles' => 'The canvas shows your CSS', 'creating-blocks' => 'Creating blocks']),
        ]);
    }

    protected function blocksForMediaLibrary(): array
    {
        return $this->doc('media-library', [
            B::rich('<p>One library shared by every site, organised like a file manager.</p><h2>Browsing and uploading</h2><ul><li><strong>Folders and breadcrumbs</strong>, with the open folder kept in the address so it can be bookmarked. A grid of cards or a list of rows. Search and tags look across every folder.</li><li><strong>Folders</strong>: create, rename, move and delete them; deleting moves the contents up a level. Drag files and folders onto folders or breadcrumbs.</li><li><strong>Uploading</strong>: a dialog, or drag and drop from the desktop, with progress per file. Images, video, audio, PDF, Office documents, text and archives, up to 10 MB per image and 50 MB per other file. Scripts, HTML and executables are refused.</li><li><strong>Tags</strong>: coloured tags shown as dots, created on the spot. <strong>Filters</strong>: type, tags, used or unused, and with or without alt text.</li><li><strong>Bulk actions</strong>: move, access and delete. Everything is also on the right-click menu.</li></ul>'),
            $this->screenshot('media', 'The media library: folders, breadcrumbs and images with their tags.'),
            B::rich('<h2>A file\'s page</h2><ul><li>A preview with a <strong>focal point</strong> to click, which cropped sizes keep in view.</li><li>Title, alt text and caption per language, and credit. <em>Write with AI</em> looks at the image to write the alt text, and the assistant can write it for every new image by itself.</li><li>Folder and tags, file details and a download, EXIF data for images and ID3 tags for audio.</li><li><strong>Replace file</strong>: the file keeps its id, so every page, link and redirect that uses it keeps working.</li><li><strong>Used on</strong>: every page and redirect that uses the file, tracked automatically.</li><li><strong>Formats</strong>: every size made from an image with its status, regenerate one size or all, and each block\'s own edited version.</li><li><strong>Access</strong>: limit a file or folder to roles or people, with read or read and write access. This only narrows the admin; files still show wherever pages use them.</li></ul>'),
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
            B::rich('<h2>Ordering and registering by hand</h2><p>Blocks from other folders, or in a set palette order, are registered from a service provider: <code>Blocks::register([HeroBlock::class, CardBlock::class])</code>. A type registered again replaces the earlier one, so a site can override a library block.</p><h2>What you get for free</h2><ul><li>validated when AI tools write it;</li><li>described to AI assistants by <code>list_block_types</code>, built from the schema;</li><li>translated block by block, and indexed for search;</li><li>served structured by the REST and GraphQL APIs: images, page links and forms come out as what they point at.</li></ul><h2>This website\'s blocks</h2><p>This website is built from 24 blocks of its own, in its <code>app/Blocks</code>. They\'re good examples to read or start from.</p><table><tr><th>Category</th><th>Blocks</th></tr><tr><td>Layout</td><td>Section band, Responsive grid, Button group</td></tr><tr><td>Content</td><td>Heading, Paragraph, Rich text, Button, Card, Callout, Accordion / FAQ, Tabs, Steps / timeline</td></tr><tr><td>Marketing</td><td>Hero, Feature, Statistic, Pricing plan, Testimonial, Team member, Logo cloud, Call to action</td></tr><tr><td>Media</td><td>Image, Gallery</td></tr><tr><td>Dynamic</td><td>Form, Page list</td></tr><tr><td>Developer</td><td>Code sample</td></tr></table><p>The page builder adds Section (columns), List, Quote, Spacer, Divider, Video, Embed, Shortcode and Code.</p>'),
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
            B::rich('<h2>The helpers</h2><p>The helpers are short forms of static methods (<code>inline_text()</code> is <code>Aurora\Support\Markup::inline()</code>), so views need no <code>@php use …</code> imports. Call the classes directly when you prefer.</p><table><tr><th>Helper</th><th>What it does</th></tr><tr><td><code>@editable(\'field\')</code></td><td>Makes the element editable on the canvas (for fields listed in <code>editables()</code>)</td></tr><tr><td><code>inline_text($text)</code></td><td>Text with <code>*highlighted*</code> words</td></tr><tr><td><code>plain_text($text)</code></td><td>Plain text, escaped</td></tr><tr><td><code>rich_text($html)</code></td><td>Rich text from a RichEditor, cleaned</td></tr><tr><td><code>shows_field($field, $value)</code></td><td>Whether to render an optional field: when it\'s filled, or when the canvas is editing so it can be filled</td></tr><tr><td><code>link_href($data)</code>, <code>link_target($data)</code></td><td>The address and target of a <code>Fields::link()</code> group; pass the prefix for another group: <code>link_href($data, \'cta\')</code></td></tr><tr><td><code>is_editing()</code></td><td>True on the canvas, for example to show placeholders</td></tr><tr><td><code>block_slot($name)</code></td><td>A container block\'s slot with the blocks in it</td></tr><tr><td><code>&lt;x-aurora::media&gt;</code></td><td>A media library image, responsive, with the block\'s crop and the focal point</td></tr></table><h2>Or as Blade directives</h2><table><tr><th>Helper</th><th>Directive</th></tr><tr><td><code>{{ inline_text($data[\'title\'] ?? \'\') }}</code></td><td><code>@inlineText($data[\'title\'] ?? \'\')</code></td></tr><tr><td><code>{{ plain_text(…) }}</code>, <code>{{ rich_text(…) }}</code></td><td><code>@plainText(…)</code>, <code>@richText(…)</code></td></tr><tr><td><code>{{ link_href($data) }}</code>, <code>{{ link_target($data) }}</code></td><td><code>@linkHref($data)</code>, <code>@linkTarget($data)</code></td></tr><tr><td><code>{{ block_slot(\'content\') }}</code></td><td><code>@blockSlot(\'content\')</code> (not <code>@slot</code>: Blade\'s components use it)</td></tr><tr><td><code>@if (shows_field(\'body\', …)) … @endif</code></td><td><code>@shows(\'body\', …) … @endshows</code></td></tr><tr><td><code>@if (is_editing()) … @endif</code></td><td><code>@editing … @else … @endediting</code></td></tr></table>'),
            $this->note('Named so they never clash', 'The helpers are named for what they read (link_href, plain_text), never bare words like href() or text() that Laravel or another package could also define.'),
            $this->seeAlso(['canvas-styles' => 'The canvas shows your CSS', 'add-your-own-block' => 'Guide: add your own block']),
        ]);
    }

    protected function blocksForCanvasStyles(): array
    {
        return $this->doc('canvas-styles', [
            B::rich('<p>The visual editor renders your blocks in the website\'s own stylesheet, so they look on the canvas exactly as they do on the site. There\'s nothing to set up: Aurora finds <code>resources/css/app.css</code> through Vite (the built file, or the dev server while <code>npm run dev</code> runs) and scopes it to the canvas with CSS <code>@scope</code>, so it can\'t touch the admin around it. Whatever your stylesheet says of the page itself (<code>:root</code>, <code>html</code>, <code>body</code>) it says of the canvas.</p><h2>Style the page in CSS, not with classes on &lt;body&gt;</h2><p>The canvas has no <code>&lt;body&gt;</code> of the site\'s, so put the page\'s font, colours and background in the stylesheet:</p>'),
            B::code(<<<'CSS'
@layer base {
    body {
        @apply bg-white font-body text-zinc-600;
    }
}
CSS, 'css', 'resources/css/app.css'),
            B::rich('<h2>Fonts</h2><p>Fonts in the stylesheet (<code>@font-face</code>, <code>@import</code>) come along. Fonts linked from your layout\'s <code>&lt;head&gt;</code> don\'t, so load them in the stylesheet too, or the canvas shows them in a fallback font.</p>'),
            B::code("@import url('https://fonts.bunny.net/css?family=inter:400,500,600,700');\n@import 'tailwindcss';", 'css', 'resources/css/app.css'),
            B::rich('<h2>Another entry</h2><p>Set <code>AURORA_WEBSITE_CSS</code> (<code>aurora.website_css</code>) to another Vite entry, or leave it empty to show blocks without the site\'s styles.</p>'),
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
            B::rich('<ul><li><strong>Every layout gets</strong> <code>$page</code>, <code>$layout</code>, <code>$preview</code> (a preview link, or a page that isn\'t live yet) and <code>$embedded</code> (drawn inside the visual editor\'s preview).</li><li><strong><code>&lt;x-aurora::blocks :page="$page" /&gt;</code></strong> renders the page\'s blocks in the current language.</li><li><strong>Adding a layout</strong>: in <strong>Settings › Templates › Layouts</strong>, give it a name, handle, icon and the Blade view it uses, which can be any view in your app, and pick the default. Pages choose their layout from cards with thumbnails, and preview links can show a page in another layout.</li><li><strong>Shared pieces</strong> such as the header, menus and footer belong in components: <code>Menu::location(\'header\')</code>, <code>Setting::get(…)</code>, and the settings your blocks\' library adds.</li></ul><h2>What Aurora serves</h2><ul><li>Pages at their address, in the layout they chose, and other languages under their prefix (<code>/nb/om-oss</code>). Drafts and scheduled pages answer 404 to visitors and show as previews to signed-in editors.</li><li>Signed preview links for drafts, scheduled pages, unpublished changes, revisions, other languages and other layouts.</li><li><code>sitemap.xml</code> of live pages, without hidden, password-protected or members-only pages, and <code>robots.txt</code>, which keeps crawlers out of <code>/admin</code> and previews.</li><li>Password-protected pages unlock for the visit; members-only pages show a sign-in page. Publish <code>aurora-views</code> to restyle them.</li><li>Redirects, checked before a 404; the page cache; <code>/files/{id}</code>; and forms posted to <code>/forms/{form}</code>.</li></ul><p>Turn <code>aurora.routes</code> off to route pages yourself with <code>Aurora\Http\Controllers\PageController</code>.</p>'),
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
            B::rich('<h2>Translations</h2><p>The <strong>Translations</strong> screen holds every label in the website and the admin, Aurora\'s, Laravel\'s and other packages\', editable in place, one tab per language, with a count of what\'s missing.</p><ul><li><strong>Filters</strong>: needs translation, where a label comes from, and edited here.</li><li><strong>Per label</strong>: edit every language at once, or reset to the lang file.</li><li><strong>For the whole list</strong>: <em>Translate all</em> with AI, <em>Import from lang files</em> (keeps edits) and <em>Remove unused</em>.</li><li>Missing labels fall back to the default language.</li></ul>'),
            $this->screenshot('translations', 'Every label, one tab per language.'),
            B::rich('<p>On the command line, <code>aurora:sync-translations</code> adds new labels from the code, <code>aurora:import-translations</code> loads the lang files and <code>aurora:export-translations</code> writes edits made in the admin back to them.</p>'),
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
            B::rich('<p>Settings is grouped into pages in a side menu, each with tabs, at <code>?tab=&lt;page&gt;&amp;section=&lt;tab&gt;</code>. Packages and the website can add their own tabs.</p><table><tr><th>Page</th><th>Tab</th><th>What it holds</th></tr><tr><td><strong>Website</strong></td><td>Branding</td><td>Site name, contact email, tagline, default share image, brand and accent colours with a live preview</td></tr><tr><td></td><td>Publishing</td><td>Whether saved changes to live pages go live straight away</td></tr><tr><td></td><td>Header, Footer</td><td>From this website\'s own set-up: the header button, announcement bar, footer note and social profiles</td></tr><tr><td><strong>Templates</strong></td><td>Layouts</td><td>The page layouts: name, handle, icon, Blade view, and which is the default</td></tr><tr><td></td><td>Blocks</td><td>Every block, with an uploadable picture for the palette</td></tr><tr><td></td><td>Menu locations</td><td>The places in templates that can show a menu</td></tr><tr><td><strong>Media</strong></td><td>Image formats</td><td>The sizes made of every image: width, height, format, quality, responsive</td></tr><tr><td><strong>Languages &amp; sites</strong></td><td>Languages, Sites</td><td>Languages and their prefixes; the websites this admin runs</td></tr><tr><td><strong>Search &amp; SEO</strong></td><td>Search index, SEO report</td><td>Try a search, reindex; every live page that could do better in search results</td></tr><tr><td><strong>Performance</strong></td><td>Page cache</td><td>Serve pages ready-made; how long to keep them; clear the cache</td></tr><tr><td><strong>Integrations</strong></td><td>REST API, GraphQL API, Webhooks</td><td>Switch the APIs on, require keys, manage keys; addresses told about content events</td></tr><tr><td><strong>Security</strong></td><td>Sign-in, Website access, API keys</td><td>Require two-factor or passkeys; limit the website to IP addresses; keys with scopes</td></tr><tr><td><strong>Appearance</strong></td><td>Admin theme</td><td>The admin\'s theme, for everyone</td></tr><tr><td><strong>AI Assistant</strong></td><td>AI Assistant</td><td>Provider, model, API key (stored encrypted), on or off, alt text for new images, instructions</td></tr><tr><td><strong>System</strong></td><td>Maintenance, Import, System info</td><td>Clear caches, send a test email, reindex; import WordPress; what the CMS runs on</td></tr></table>'),
            $this->screenshot('settings', 'Settings › Website › Branding, with the side menu of every settings page.'),
            $this->seeAlso(['your-own-settings' => 'Add your own settings']),
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
            B::rich('<p>The admin\'s look is a theme package. Aurora ships <strong>Borealis</strong> (deep violet and night blue, the default) and <strong>Minimal</strong> (quiet, flat greys). The theme is picked for everyone under <strong>Settings › Appearance</strong>, and a theme can support light mode, dark mode or both. The mode switch only appears when it has both.</p>'),
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
            B::rich('<ul><li><strong>Its picture</strong> is one for every mode, or one per mode (<code>preview: [\'light\' => …, \'dark\' => …]</code>): the theme picker shows the one for the mode the person is in.</li><li><strong>The CMS\'s components</strong> (canvas, inspector, media library and Settings screens) are styled once in <code>shipaurora/cms</code>\'s <code>components.css</code>, which every theme imports before its own styles.</li><li><strong>A theme</strong> styles Filament\'s <code>fi-*</code> classes and restyles <code>cms-*</code> hooks where it wants something different, with its own tokens. The Minimal theme (<code>shipaurora/minimal-theme</code>) is a good one to copy.</li></ul>'),
        ]);
    }

    protected function blocksForMovingFromWordpress(): array
    {
        return $this->doc('moving-from-wordpress', [
            B::rich('<p>Export the site in WordPress (<em>Tools › Export</em>, <em>All content</em>), then import it under <strong>Settings › System › Import</strong>, or on the command line:</p>'),
            B::code('php artisan aurora:import-wordpress export.xml --site=main', 'bash'),
            B::rich('<ul><li>Pages come in as they are in WordPress, published or draft.</li><li>Blog posts come in too, under a Blog page (<code>--without-posts</code> leaves them out).</li><li>Images are downloaded into the media library (<code>--without-media</code> skips them).</li><li>Menus are rebuilt.</li></ul>'),
            $this->screenshot('import', 'Settings › System › Import: upload a WordPress export.'),
            $this->seeAlso(['moving-from-another-cms' => 'Guide: moving from another CMS']),
        ]);
    }

    /* ── AI and integrations ──────────────────────────────────────────── */

    protected function blocksForAi(): array
    {
        return $this->doc('ai', [
            B::rich('<h2>The MCP server</h2><p>At <code>POST /mcp</code> (Streamable HTTP, OAuth with Laravel Passport).</p><ul><li><strong>Connecting</strong>: Claude Desktop and claude.ai add it as a <em>custom connector</em>. Claude Code, VS Code and Cursor sign in through the browser. Scripts use personal access tokens. <strong>AI › Connect AI apps</strong> has step-by-step setup with copy buttons, the apps and tokens you\'ve connected, and what your assistant can do.</li><li><strong>Tools</strong>: sites and identity (<code>whoami</code>, <code>list_sites</code>); pages (<code>list_pages</code>, <code>get_page</code>, <code>create_page</code>, <code>update_page</code>, <code>delete_page</code>, <code>publish_page</code>); blocks (<code>list_block_types</code>, <code>set_page_blocks</code>, <code>add_block</code>, <code>update_block</code>, <code>move_block</code>, <code>remove_block</code>); history (<code>list_revisions</code>, <code>restore_revision</code>); media (<code>list_images</code>, <code>upload_image_from_url</code>); menus (<code>get_menus</code>, <code>add_menu_item</code>, <code>update_menu_item</code>, <code>remove_menu_item</code>); and <code>list_forms</code>, <code>list_layouts</code>, <code>seo_report</code>.</li><li><strong>Permissions</strong>: the AI acts as the person who connected it. Tools their role can\'t use aren\'t even listed. Publishing needs the publish permission. Changes to live pages wait as unpublished changes, and appear in the page history under the person\'s name.</li><li><strong>Restricted callbacks</strong>: OAuth clients may only redirect to Claude, localhost or the Claude, Cursor and VS Code app schemes.</li><li><strong>Development</strong>: expose the site (for example <code>herd share</code>) and set <code>MCP_PUBLIC_URL</code>. <code>php artisan mcp:start cms</code> runs a local stdio server as <code>MCP_USER</code>.</li></ul>'),
            $this->screenshot('connect-ai', 'AI › Connect AI apps: setup for each app, with copy buttons.'),
            B::rich('<h2>The assistant in the admin</h2><p>Set up under <strong>Settings › AI Assistant</strong>.</p><ul><li><strong>Providers</strong>: Anthropic (Claude), OpenAI, Google Gemini, Mistral, Groq, xAI, DeepSeek, or any OpenAI-compatible or Anthropic-compatible endpoint, including OpenRouter, Ollama and LM Studio.</li><li><strong>What it uses</strong>: the same tools as MCP, with the same permissions, logged as "Assistant".</li><li><strong>What it knows</strong>: the screen you\'re on, so "make the heading shorter" needs no page name.</li><li><strong>Attachments</strong>: images, PDFs, text and CSV. Uploaded images only join the media library when a page uses them.</li><li><strong>Conversations</strong> are kept and can be exported as Markdown, and open editors redraw when it changes the page.</li></ul><h2>Write with AI, translation and alt text</h2><ul><li><strong>Write with AI</strong> is a sparkle button on text fields. It writes from the rest of the form and the page, with an optional instruction, and for alt text it looks at the image.</li><li><strong>AI translation</strong> translates pages block by block, form fields and every label.</li><li><strong>Alt text, written automatically</strong>: switch on <em>Write alt text for images automatically</em> under Settings › AI Assistant, and the assistant describes each image added to the media library, in the website\'s language, on the queue. Switching it on also describes the images already there without alt text. Alt text someone wrote is never replaced.</li></ul><h2>The AI activity log</h2><p><strong>AI › Activity log</strong> records every tool call by an AI: the channel (an app over MCP, or the assistant), the tool, the person it acted for, the outcome and the arguments, with a link to the page it changed.</p>'),
            $this->seeAlso(['let-claude-draft-a-page' => 'Guide: let Claude draft a page', 'users-and-roles' => 'Roles and permissions']),
        ]);
    }

    protected function blocksForHeadlessApis(): array
    {
        return $this->doc('headless-apis', [
            B::rich('<p>Switch either API on under <strong>Settings › Integrations</strong>. Both are read-only and only show what visitors may see: live pages that aren\'t password-protected or members-only, files those pages use, and public settings.</p>'),
            $this->screenshot('api', 'Settings › Integrations › REST API: on, whether a key is needed, and how to call it.'),
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
            B::rich('<p>Publish the config with <code>php artisan vendor:publish --tag=aurora-config</code> (<code>config/aurora.php</code>).</p><table><tr><th>Key</th><th>Env</th><th>Default</th><th>What it does</th></tr><tr><td><code>name</code></td><td><code>AURORA_NAME</code></td><td>Aurora CMS</td><td>The CMS\'s name in the admin</td></tr><tr><td><code>user_model</code></td><td></td><td><code>Aurora\Models\User</code></td><td>The user model</td></tr><tr><td><code>demo_login</code></td><td><code>AURORA_DEMO_LOGIN</code></td><td><code>false</code></td><td>One-click sign-in as the demo accounts. Never in production</td></tr><tr><td><code>theme</code></td><td><code>AURORA_THEME</code></td><td>Borealis</td><td>The admin theme until one is chosen in Settings › Appearance</td></tr><tr><td><code>website_css</code></td><td><code>AURORA_WEBSITE_CSS</code></td><td><code>resources/css/app.css</code></td><td>The website\'s stylesheet for the canvas</td></tr><tr><td><code>block_path</code></td><td></td><td><code>app/Blocks</code></td><td>Where the site\'s blocks are found</td></tr><tr><td><code>block_namespace</code></td><td></td><td>from the path</td><td>Their namespace</td></tr><tr><td><code>routes</code></td><td><code>AURORA_ROUTES</code></td><td><code>true</code></td><td>Whether Aurora serves the website\'s pages</td></tr><tr><td><code>translations_path</code></td><td><code>AURORA_TRANSLATIONS_PATH</code></td><td></td><td>Where <code>aurora:export-translations</code> writes</td></tr></table><h2>Other environment variables</h2><p><code>MCP_PUBLIC_URL</code> (the public address AI connectors reach in development) and <code>MCP_USER</code> (who the local stdio MCP server acts as).</p><h2>Publish tags</h2><p><code>aurora-config</code>, <code>aurora-layouts</code>, <code>aurora-views</code> (the password, sign-in and site closed pages) and <code>aurora-stubs</code> (the <code>make-block</code> stubs).</p>'),
        ]);
    }

    protected function blocksForArtisanCommands(): array
    {
        return $this->doc('artisan-commands', [
            B::rich('<table><tr><th>Command</th><th>What it does</th></tr><tr><td><code>aurora:install</code></td><td>Sets up the CMS: key, database, required content, labels, admin account. Options: <code>--fresh</code>, <code>--force</code>, <code>--seed</code>, <code>--site-name</code>, <code>--language</code>, <code>--languages</code>, <code>--name</code>, <code>--email</code>, <code>--password</code>, <code>--interactive-only</code> (the starter app\'s <code>create-project</code> runs it this way: without a terminal to answer in, it says how to finish)</td></tr><tr><td><code>make:aurora-user</code></td><td>Creates a user and gives them a role, asking the same questions as <code>make:filament-user</code> plus the role. Options: <code>--name</code>, <code>--email</code>, <code>--password</code>, <code>--role</code> (a role\'s name, e.g. <code>Editor</code>)</td></tr><tr><td><code>aurora:make-block {name}</code></td><td>Creates a block class and its view. Options: <code>--container</code>, <code>--category</code>, <code>--icon</code>, <code>--force</code></td></tr><tr><td><code>aurora:import-wordpress {file}</code></td><td>Imports a WordPress export. Options: <code>--site</code>, <code>--without-posts</code>, <code>--without-media</code></td></tr><tr><td><code>aurora:sync-translations</code></td><td>Adds new labels from the code to the default lang file, then imports the lang files</td></tr><tr><td><code>aurora:import-translations</code></td><td>Imports labels from the lang files, keeping edits made in the admin</td></tr><tr><td><code>aurora:export-translations</code></td><td>Writes labels edited in the admin back to lang files</td></tr><tr><td><code>mcp:start cms</code></td><td>Runs the MCP server over stdio, for local development</td></tr></table>'),
        ]);
    }

    protected function blocksForPackagesAndDevelopment(): array
    {
        return $this->doc('packages-and-development', [
            B::rich('<h2>Packages</h2><table><tr><th>Package</th><th>Namespace</th><th>Contents</th></tr><tr><td><code>shipaurora/aurora</code></td><td><code>App\</code></td><td>The application template a new website starts from (<code>composer create-project</code>)</td></tr><tr><td><code>shipaurora/cms</code></td><td><code>Aurora\</code></td><td>The CMS: models, Filament admin, visual editor extensions, MCP server, REST and GraphQL APIs, migrations, views, translations (English and Norwegian), admin scripts and the shared component styles</td></tr><tr><td><code>shipaurora/borealis-theme</code></td><td><code>Aurora\Borealis\</code></td><td>Borealis, the default admin theme, prebuilt</td></tr><tr><td><code>shipaurora/minimal-theme</code></td><td><code>Aurora\Minimal\</code></td><td>The Minimal admin theme, prebuilt</td></tr></table><h2>Working with AI coding agents</h2><p>Aurora ships guidelines and a skill for Laravel Boost, so coding agents know how to build blocks, layouts and settings in an Aurora project. Run <code>php artisan boost:install</code> and they\'re picked up with the rest.</p><h2>Development</h2>'),
            B::code(<<<'BASH'
composer dev                         # server, queue, logs and Vite
npm run build                        # the website's assets and Aurora's prebuilt admin assets
php artisan test --parallel          # the test suite, in parallel (ParaTest)
vendor/bin/pint --dirty              # code style
BASH, 'bash'),
            B::rich('<p>The test suite (PHPUnit) covers permissions on every surface, the publishing workflow and unpublished changes, the visual editor (every block can be inserted, edited and saved), the public website, the APIs and their scopes, MCP tools, media, forms, translations and settings. Tests never reach the network: a request that isn\'t faked fails.</p><h2>License</h2><p>Aurora CMS is open-source software licensed under the MIT license. Copyright © 2026 Thomas Alrek.</p>'),
        ]);
    }
}
