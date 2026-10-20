<?php

namespace Database\Seeders\Demo\Pages;

use Database\Seeders\Demo\Support\B;

/**
 * The docs website's product pages: the homepage, why Aurora, the feature tour, the release
 * notes, the component library and contact.
 */
trait ProductPages
{
    protected function blocksForHome(): array
    {
        return [
            ['type' => 'hero', 'data' => [
                'layout' => 'centered',
                'badge' => 'New',
                'badge_text' => 'Aurora 1.0 is out',
                'headline' => 'The CMS your editors *enjoy* and your developers trust',
                'subheadline' => "Aurora is a content management system for Laravel, built on Filament.\nEditors compose pages visually on the real design. Developers keep plain Laravel. AI assistants work with exactly the permissions of the person using them.",
                'primary_label' => 'Read the docs',
                'primary_type' => 'page',
                'primary_page' => $this->id('installation'),
                'secondary_label' => 'Why Aurora?',
                'secondary_type' => 'page',
                'secondary_page' => $this->id('why-aurora'),
            ]],
            B::band([
                $this->screenshot('editor'),
            ], 'white', 'md', 'wide'),
            B::band([
                ['type' => 'logo-cloud', 'data' => [
                    'title' => 'Built on tools Laravel teams already know',
                    'marquee' => true,
                    'logos' => array_map(fn ($name) => ['name' => $name], ['Laravel', 'Filament', 'Livewire', 'Tailwind CSS', 'Laravel Scout', 'Laravel MCP', 'Spatie Media Library', 'Passport']),
                ]],
            ], 'white', 'md'),
            B::band([
                B::heading('One system for the whole team', 'Who it is for', 'Editors compose pages, developers own the blocks and the code, and AI assistants help both, inside the same permissions.', 'xl', 'center'),
                B::spacer(),
                ['type' => 'tabs', 'data' => ['style' => 'pills', 'items' => [
                    ['label' => 'Editors', 'title' => 'Work on the page itself', 'body' => "Drag blocks onto the canvas, type straight into headings, lists and FAQ answers, and switch between desktop, tablet and mobile. Preview the unsaved page inside the real website.\nSave as often as you like: changes to a live page wait until someone publishes them, and every save is a revision you can restore."],
                    ['label' => 'Developers', 'title' => 'Just Laravel', 'body' => "Your website is a Laravel app and the CMS is a package in it. A block is a PHP class and a Blade view: run aurora:make-block and it appears in the editor, the APIs, search and the AI tools at once.\nNo flat files, no template language, no second control panel to learn. Upgrading is composer update."],
                    ['label' => 'AI assistants', 'title' => 'Assistants with the same keys as you', 'body' => "Chat with the assistant in the admin, or connect Claude, Cursor or VS Code over MCP. They act as the person who connected them: an author's assistant drafts, an editor's assistant publishes.\nEvery AI action is logged, and every change is a revision with the person's name on it."],
                ]]],
            ], 'muted', 'xl', 'wide'),
            B::band([
                B::columns([
                    [$this->screenshot('unpublished')],
                    [
                        B::heading('A publishing workflow you can *trust*', 'Safe by default', 'Drafts, review, scheduling and revisions come built in, with no add-ons.'),
                        ['type' => 'list', 'data' => ['marker' => 'check', 'items' => "Saved changes to a live page wait until someone publishes them\nAuthors send pages for review; editors are notified\nPublish and unpublish dates, without a cron job\nEdit locks, so nobody overwrites anybody\nA revision for every save, restorable in one click"]],
                        B::buttons([
                            B::button('The publishing workflow', $this->id('publishing'), 'primary', icon: 'arrow-right'),
                            B::button('All features', $this->id('features'), 'link'),
                        ]),
                    ],
                ], '1-1'),
            ], 'white', 'xl', 'wide'),
            B::band([
                B::heading('Everything an agency needs', 'Highlights', null, 'lg', 'center'),
                B::spacer(),
                B::grid([
                    B::feature('squares-plus', 'A visual editor on the real design', 'The canvas renders your own Blade components in your own stylesheet. What editors see is what visitors get.', 'card'),
                    B::feature('cube', 'Structured content', 'Every block is a typed PHP class with a Filament schema, stored as validated JSON for the website, the APIs and AI.', 'card'),
                    B::feature('photo', 'Media, forms and menus', 'A media library with focal points and formats, forms with conditional fields, menus that follow your pages.', 'card'),
                    B::feature('globe-europe-africa', 'Languages and sites', 'Translate pages block by block, and run several websites with their own domains from one admin.', 'card'),
                    B::feature('sparkles', 'AI that respects permissions', 'An MCP server for Claude and others, an assistant in the admin, Write with AI and automatic alt text.', 'card', 'accent'),
                    B::feature('code-bracket', 'Headless when you need it', 'REST and GraphQL APIs with scoped keys, webhooks, search and a page cache that clears itself.', 'card', 'success'),
                ], 3),
            ], 'muted', 'xl', 'wide'),
            B::band([
                B::heading('Small enough to understand, complete enough to ship', 'In numbers', null, 'lg', 'center'),
                B::spacer(),
                B::grid([
                    B::stat('33', 'Blocks out of the box', null, null, 'From this website and the page builder'),
                    B::stat('25', 'MCP tools', null, null, 'For pages, blocks, menus, media and more'),
                    B::stat('2', 'Languages in the admin', null, null, 'English and Norwegian, every label editable'),
                    B::stat('490', 'Feature tests', '+', null, 'Run in parallel on every change'),
                ], 4),
            ], 'aurora', 'xl', 'wide'),
            B::band([
                B::columns([
                    [
                        B::heading('Start a website in minutes', 'For developers', 'New websites start from the application template: the CMS comes in through Composer, the website\'s code stays yours. Already have a Laravel app? composer require shipaurora/cms.'),
                        B::buttons([
                            B::button('Installation', $this->id('installation'), 'primary', icon: 'book-open'),
                            B::button('Philosophy and architecture', $this->id('architecture'), 'link'),
                        ]),
                    ],
                    [B::code("composer create-project shipaurora/aurora my-site   # asks a few questions and sets it all up\ncd my-site", 'bash', null, 'The installer sets up the database, roles and your first admin account.')],
                ], '1-1'),
            ], 'white', 'xl', 'wide'),
            B::band([
                B::heading('From the guides', 'Learn', null, 'lg'),
                B::spacer('sm'),
                ['type' => 'page-list', 'data' => ['source' => 'selected', 'page_ids' => [$this->id('build-a-landing-page'), $this->id('let-claude-draft-a-page'), $this->id('add-your-own-block')], 'link_label' => 'Read the guide']],
            ], 'muted', 'xl', 'wide'),
            B::band([
                ['type' => 'cta', 'data' => [
                    'style' => 'gradient',
                    'title' => 'Start your first site',
                    'text' => 'Start from the application template and your team has a website they can edit on the real design in minutes.',
                    'button_label' => 'Installation',
                    'button_type' => 'page',
                    'button_page' => $this->id('installation'),
                ]],
            ], 'white', 'lg', 'wide'),
        ];
    }

    protected function blocksForWhyAurora(): array
    {
        return [
            ['type' => 'hero', 'data' => [
                'layout' => 'split',
                'image' => 'demo/screen-dashboard.jpg',
                'badge' => 'Why',
                'badge_text' => 'The thinking behind Aurora',
                'headline' => 'The CMS is a dependency, the website is *the app*',
                'subheadline' => 'Aurora started from one question: what if a client site\'s CMS were simply part of the Laravel application, rather than a platform the application lives inside?',
                'primary_label' => 'Philosophy and architecture',
                'primary_type' => 'page',
                'primary_page' => $this->id('architecture'),
                'secondary_label' => '',
            ]],
            B::band([
                B::heading('Four principles', 'What follows from that', null, 'lg', 'center'),
                B::spacer(),
                B::grid([
                    B::feature('cube-transparent', 'The CMS is a dependency', 'Everything the CMS is comes in through Composer. The app is the website: its layouts, components and blocks. Upgrading never touches the site.', 'card'),
                    B::feature('cursor-arrow-rays', 'Edit the real thing', 'The canvas renders the same Blade components the website renders, with the site\'s own CSS. What editors see is what visitors get.', 'card'),
                    B::feature('cube', 'Content stays structured', 'Visual doesn\'t mean free-form HTML. Each block declares its fields, so content is typed, validated, translatable and machine-readable.', 'card'),
                    B::feature('key', 'Permissions are the boundary', 'The admin, the editor, bulk actions, MCP and the assistant all go through the same policies. An assistant never does more than its person.', 'card', 'success'),
                ], 2),
            ], 'muted', 'xl', 'wide'),
            B::band([
                B::heading('How Aurora differs from Statamic', 'Compared', 'Statamic is an excellent CMS, and Aurora borrows ideas from it. The difference is where each one puts the CMS relative to Laravel.', 'lg'),
                B::spacer('sm'),
                B::rich('<table><tr><th></th><th>Statamic</th><th>Aurora</th></tr><tr><td><strong>Storage</strong></td><td>Flat files by default (Stache); Eloquent through a driver</td><td>Eloquent and the database, natively</td></tr><tr><td><strong>Admin</strong></td><td>Its own control panel, built with Vue</td><td>Filament: Laravel\'s own admin framework, with Livewire and Blade</td></tr><tr><td><strong>Templates</strong></td><td>Antlers, or Blade</td><td>Blade components only</td></tr><tr><td><strong>Content model</strong></td><td>Collections and blueprints in YAML</td><td>Pages made of blocks; each block a PHP class with a Filament schema</td></tr><tr><td><strong>Editing</strong></td><td>Form-based entries; Bard and Replicator for flexible fields</td><td>A visual canvas on the real design, plus forms for page settings</td></tr><tr><td><strong>Extending</strong></td><td>Addons with their own conventions</td><td>Ordinary Laravel: service providers, Blade, Eloquent, Filament plugins</td></tr><tr><td><strong>AI</strong></td><td>Through addons</td><td>Built in: an MCP server with OAuth, an assistant, AI writing and translation, all bound to permissions</td></tr><tr><td><strong>Headless</strong></td><td>REST and GraphQL in the Pro edition</td><td>REST and GraphQL included, with scoped API keys</td></tr><tr><td><strong>Licence</strong></td><td>Commercial Pro licence per site</td><td>MIT</td></tr></table>', 'wide'),
            ], 'white', 'xl', 'wide'),
            B::band([
                B::columns([
                    [B::heading('Why build it this way?', 'The reasons', null, 'md')],
                    [B::rich('<ul><li><strong>One stack.</strong> A team that knows Laravel and Filament knows Aurora. There\'s no second framework to learn for the control panel and no template language to switch to.</li><li><strong>The database is the source of truth.</strong> Content works with transactions, queues, Scout, policies, factories and tests like any other Laravel data, and scales the way the app scales.</li><li><strong>Typed blocks instead of blueprints.</strong> A block is a class you can read, test, refactor and autocomplete. Adding one is <code>php artisan aurora:make-block</code>.</li><li><strong>Built for the AI era.</strong> Blocks describe themselves (fields, options, hints), so assistants can build and edit pages reliably, and the permission model makes that safe to allow.</li></ul>')],
                ], '1-2', 'xl', 'start'),
                B::spacer('lg'),
                B::columns([
                    [B::heading('What editors and clients notice first', 'What makes it better', null, 'md')],
                    [B::rich('<ul><li><strong>No preview guessing.</strong> The canvas is the design. Inline editing covers headings, text, buttons and list items such as FAQ questions and pricing features.</li><li><strong>Safe by default.</strong> Unpublished changes, review, revisions, edit locks and scheduling come built in.</li><li><strong>One admin for many sites and languages.</strong> Pages are translated block by block with AI help, and every site has its own domain, languages and people.</li><li><strong>Fast websites.</strong> Pages can be served from a cache that clears itself on any change, and image formats are made in the background in WebP or AVIF.</li><li><strong>Plain-language admin.</strong> Roles, settings, warnings and notifications are written for editors, in English and Norwegian, and every label can be translated.</li><li><strong>AI with an audit trail.</strong> Clients can let assistants work on their site because it acts as them, can be limited by role, and is logged.</li></ul>')],
                ], '1-2', 'xl', 'start'),
            ], 'muted', 'xl', 'wide'),
            B::band([
                B::heading('Questions people ask', 'FAQ', null, 'lg', 'center'),
                B::spacer(),
                ['type' => 'accordion', 'data' => ['first_open' => true, 'faq_schema' => true, 'items' => [
                    ['question' => 'Can I use my own design?', 'answer' => 'Yes. Blocks are Blade views in your app, styled however you like. The visual editor shows them in your website\'s own stylesheet, scoped to the canvas, so editors always see your design.'],
                    ['question' => 'Does Aurora work with an existing Laravel site?', 'answer' => 'Yes. Starting from the template is recommended, but composer require shipaurora/cms and php artisan aurora:install add it to a Laravel 13 app. Then add your own blocks.'],
                    ['question' => 'Where does my content live?', 'answer' => 'In your database. Pages store their blocks as JSON, media goes to whichever filesystem disk you configure, and nothing is sent anywhere unless you connect an AI provider.'],
                    ['question' => 'Do I have to use the AI features?', 'answer' => 'No. The assistant stays off until someone adds a provider and an API key under Settings, and connecting AI apps is a separate permission per role.'],
                    ['question' => 'Can I use it headless?', 'answer' => 'Yes. Switch on the REST or GraphQL API under Settings › Integrations. Both are read-only, only show what visitors may see, and can require API keys with scopes.'],
                    ['question' => 'Which languages does the admin speak?', 'answer' => 'English and Norwegian out of the box. Every label can be translated from the Translations screen, and each person picks their own admin language.'],
                ]]],
            ], 'white', 'xl'),
            B::band([
                ['type' => 'cta', 'data' => ['style' => 'dark', 'title' => 'Convinced, or curious?', 'text' => 'The quickest way to judge a CMS is to use it. Start a project from the template and see for yourself in a few minutes.', 'button_label' => 'Installation', 'button_type' => 'page', 'button_page' => $this->id('installation')]],
            ], 'white', 'lg', 'wide'),
        ];
    }

    protected function blocksForFeatures(): array
    {
        $group = fn (string $eyebrow, string $title, string $intro, array $features, string $background, string $screenshot, string $doc): array => B::band([
            B::heading($title, $eyebrow, $intro, 'lg'),
            B::spacer(),
            B::grid($features, 3),
            B::spacer('lg'),
            $this->screenshot($screenshot),
            B::spacer('sm'),
            B::buttons([B::button('Read more in the docs', $this->id($doc), 'link', icon: 'arrow-right')]),
        ], $background, 'xl', 'wide');

        return [
            B::band([
                B::heading('Everything Aurora does', 'Features', 'A tour of the admin, from the visual editor to the APIs, with a link to the docs for each part.', 'xl', 'left', 'h1'),
            ], 'brand-soft', 'lg', 'wide'),
            $group('Editing', 'Compose pages on the real design', 'The canvas renders the website\'s own components and stylesheet.', [
                B::feature('squares-plus', 'Visual editor', 'A palette of blocks, drag and drop into bands, columns and grids, and an inspector for every field.'),
                B::feature('pencil-square', 'Inline editing', 'Type into headings, paragraphs, buttons and list items. Asterisks highlight words.'),
                B::feature('device-phone-mobile', 'Every width', 'Desktop, tablet and mobile on the canvas, and a preview inside the real site.'),
                B::feature('scissors', 'Image edits', 'Crop, rotate and adjust each use of an image without touching the original.'),
                B::feature('arrow-uturn-left', 'Undo, copy and paste', 'Undo and redo, copy blocks and styles, resize columns, right-click for more.'),
                B::feature('language', 'Translate on the canvas', 'One structure for every language; untranslated text is highlighted, AI fills it in.'),
            ], 'white', 'editor', 'visual-editor'),
            $group('Publishing', 'Safe by default', 'Nothing reaches the website by accident.', [
                B::feature('clock', 'Changes waiting', 'Saved changes to a live page wait until someone publishes them, unless a site says otherwise.'),
                B::feature('check-badge', 'Review', 'Authors send pages for review; everyone who may publish is notified.'),
                B::feature('calendar-days', 'Scheduling', 'Publish and unpublish dates, checked on every request: no cron needed.'),
                B::feature('lock-closed', 'Edit locks', 'One person edits a page at a time; others can take over.'),
                B::feature('clock', 'Revisions', 'Every save from every surface, previewed and restored in one click.'),
                B::feature('arrows-right-left', 'Redirects', 'Change a live page\'s address and the old one keeps working, per language.'),
            ], 'muted', 'unpublished', 'publishing'),
            $group('Content', 'Media, forms and menus', 'The parts every website needs, done properly.', [
                B::feature('photo', 'Media library', 'Folders, tags, focal points, image formats in WebP or AVIF, and where each file is used.'),
                B::feature('document-text', 'Forms', 'Conditional fields, file uploads, spam protection, email notifications and CSV export.'),
                B::feature('bars-3', 'Menus', 'Menus of any depth whose page items follow the page when its address changes.'),
            ], 'white', 'media', 'media-library'),
            $group('Languages and sites', 'Built for more than one', 'One admin for every language and every website.', [
                B::feature('language', 'Content in every language', 'Pages, menus, media text and forms, each with its own address per language.'),
                B::feature('chat-bubble-left-right', 'Labels you can edit', 'Every label in the admin and the website, translated from one screen, with AI.'),
                B::feature('globe-europe-africa', 'Several websites', 'Each with its own domain, pages, menus, settings, languages and people.'),
            ], 'muted', 'languages', 'languages'),
            $group('People and security', 'The right access for everyone', 'Roles in plain language, modern sign-in and an audit trail.', [
                B::feature('user-group', 'Roles & permissions', 'Ready-made roles, your own roles, and permissions written for editors.'),
                B::feature('finger-print', 'Passkeys & two-factor', 'Passkeys, authenticator apps and recovery codes, and switches to require them.'),
                B::feature('clipboard-document-list', 'Activity log', 'Who created, changed or deleted what, and which fields changed.'),
            ], 'white', 'roles', 'users-and-roles'),
            $group('AI', 'Assistants you can trust', 'Helpful, and never more powerful than the person using them.', [
                B::feature('link', 'MCP for Claude and more', 'Claude Desktop, claude.ai, Claude Code, Cursor and VS Code connect over OAuth.', 'soft', 'accent'),
                B::feature('sparkles', 'Assistant in the admin', 'The same tools as MCP, with the same permissions, aware of the screen you\'re on.', 'soft', 'accent'),
                B::feature('pencil', 'Write with AI', 'A sparkle on text fields, AI translation, and alt text written for every new image.', 'soft', 'accent'),
            ], 'muted', 'connect-ai', 'ai'),
            $group('Developers', 'Headless, fast and open', 'For the website, apps and everything around it.', [
                B::feature('code-bracket', 'REST and GraphQL', 'Read-only APIs with structured blocks, and API keys with scopes.'),
                B::feature('bolt', 'Page cache', 'Finished pages served without running them again, cleared on any change.'),
                B::feature('signal', 'Webhooks and events', 'Signed webhooks and Laravel events when pages and forms change.'),
            ], 'white', 'api', 'headless-apis'),
            B::band([
                ['type' => 'cta', 'data' => ['style' => 'brand', 'title' => 'See the building blocks', 'text' => 'Every block this website is built with, on one page.', 'button_label' => 'Open the component library', 'button_type' => 'page', 'button_page' => $this->id('components')]],
            ], 'white', 'lg', 'wide'),
        ];
    }

    protected function blocksForReleaseNotes(): array
    {
        return [
            B::band([
                B::heading('Release notes', 'What\'s new', 'What changed in each version of Aurora.', 'xl', 'left', 'h1'),
            ], 'brand-soft', 'lg', 'narrow'),
            B::band([
                B::heading('Aurora 1.0', 'The first release', 'A content management system for Laravel, built on Filament: everything in these docs.', 'md', 'left', 'h2'),
                ['type' => 'list', 'data' => ['marker' => 'check', 'items' => "A visual editor on the real design, showing the website's own stylesheet\nStructured blocks: a PHP class and a Blade view each, with helpers for the views\nDrafts, review, scheduling, unpublished changes, revisions and edit locks\nA media library with focal points, image formats and access per file\nForms, menus, redirects and a trash\nLanguages, translations and several websites from one admin\nRoles in plain language, passkeys, two-factor sign-in and an activity log\nAn MCP server, an assistant in the admin, Write with AI and automatic alt text\nREST and GraphQL APIs with scoped keys, webhooks, search and a page cache\nTwo admin themes, Borealis and Minimal\nMIT licensed"]],
            ], 'white', 'lg', 'narrow'),
        ];
    }

    protected function blocksForComponents(): array
    {
        return [
            B::band([
                B::heading('Component library', 'Every block, one page', 'Every block this website is built with (app/Blocks). Open this page in the visual editor to see how it is put together, or ask the assistant to build something new from them.', 'xl', 'left', 'h1'),
            ], 'brand-soft', 'lg', 'wide'),
            B::band([
                B::heading('Layout', 'Bands, columns and grids', 'Section bands hold columns, columns hold grids, grids hold cards. As deep as the layout needs.', 'md'),
                B::spacer('sm'),
                B::columns([
                    [B::card('Column one', 'A two-column section with a card in each column.', null, 'section', null, 'bordered')],
                    [B::columns([[B::card('Nested', 'A section inside a column.', null, null, null, 'bordered')], [B::card('Columns', 'Still responsive.', null, null, null, 'bordered')]], '1-1', 'md', 'stretch')],
                ], '1-2', 'lg', 'stretch'),
            ], 'white', 'lg', 'wide'),
            B::band([
                B::heading('Typography', null, null, 'md'),
                B::spacer('sm'),
                B::columns([
                    [
                        B::heading('Display heading with a *highlight*', 'Eyebrow', 'An introduction paragraph sits beneath the heading.', 'xl', 'left', 'h3'),
                        B::text('A lead paragraph is larger and darker. Use it for the first paragraph after a heading.', 'lead'),
                        B::text('A regular paragraph. Editors type directly into it on the canvas, and line breaks are kept.'),
                    ],
                    [B::rich('<h3>Rich text</h3><p>Rich text supports <strong>bold</strong>, <em>italic</em>, <a href="#">links</a>, <code>code</code> and more.</p><ul><li>Bullet lists</li><li>Numbered lists</li><li>Tables</li></ul><blockquote>And quotes, for good measure.</blockquote>')],
                ], '1-1', 'xl', 'start'),
            ], 'white', 'lg', 'wide'),
            B::band([
                B::heading('Code', 'For docs and guides', 'Shown with its file name and a copy button, never run.', 'md'),
                B::spacer('sm'),
                B::columns([
                    [B::code('php artisan aurora:make-block "Pricing table"', 'bash')],
                    [B::code("<h3 @editable('title')>{{ inline_text(\$data['title'] ?? '') }}</h3>", 'blade', 'resources/views/components/blocks/card.blade.php')],
                ], '1-1', 'lg', 'start'),
            ], 'muted', 'lg', 'wide'),
            B::band([
                B::heading('Buttons', null, null, 'md'),
                B::spacer('sm'),
                B::buttons([
                    B::button('Primary', $this->id('home')),
                    B::button('Secondary', $this->id('home'), 'secondary'),
                    B::button('Outline', $this->id('home'), 'outline'),
                    B::button('Ghost', $this->id('home'), 'ghost'),
                    B::button('Text link', $this->id('home'), 'link'),
                    B::button('With icon', $this->id('home'), 'primary', icon: 'rocket-launch'),
                ]),
            ], 'white', 'md', 'wide'),
            B::band([
                B::heading('Callouts', null, null, 'md'),
                B::spacer('sm'),
                B::grid([
                    ['type' => 'alert', 'data' => ['variant' => 'info', 'title' => 'Info', 'body' => 'Neutral information.']],
                    ['type' => 'alert', 'data' => ['variant' => 'success', 'title' => 'Success', 'body' => 'Something went well.']],
                    ['type' => 'alert', 'data' => ['variant' => 'warning', 'title' => 'Warning', 'body' => 'Mind the gap.']],
                    ['type' => 'alert', 'data' => ['variant' => 'danger', 'title' => 'Danger', 'body' => 'This cannot be undone.']],
                ], 2),
            ], 'muted', 'lg', 'wide'),
            B::band([
                B::heading('Features & statistics', null, null, 'md'),
                B::spacer('sm'),
                B::grid([
                    B::feature('bolt', 'Soft', 'Tinted icon tile.'),
                    B::feature('rocket-launch', 'Solid', 'Filled icon tile.', 'solid'),
                    B::feature('cube-transparent', 'Outline', 'Outlined icon tile.', 'outline'),
                    B::feature('squares-2x2', 'Card', 'The whole feature in a card.', 'card', 'accent'),
                ], 4),
                B::spacer('lg'),
                B::grid([B::stat('24', 'Blocks in this library'), B::stat('9', 'More from the page builder'), B::stat('1', 'Command to make your own')], 3),
            ], 'dark', 'lg', 'wide'),
            B::band([
                B::heading('Interactive', null, null, 'md'),
                B::spacer('sm'),
                B::columns([
                    [['type' => 'accordion', 'data' => ['first_open' => true, 'faq_schema' => false, 'items' => [
                        ['question' => 'Accordion item one', 'answer' => 'Click a question to expand it. On the canvas, click the text to edit it.'],
                        ['question' => 'Accordion item two', 'answer' => 'Answers keep their line breaks.'],
                    ]]]],
                    [['type' => 'tabs', 'data' => ['style' => 'underline', 'items' => [
                        ['label' => 'First tab', 'title' => 'Tabs', 'body' => 'Switch between panels without leaving the page.'],
                        ['label' => 'Second tab', 'title' => 'Underline style', 'body' => 'Tabs come in pill and underline styles.'],
                    ]]]],
                ], '1-1', 'xl', 'start'),
                B::spacer('lg'),
                ['type' => 'steps', 'data' => ['layout' => 'horizontal', 'items' => [
                    ['title' => 'Steps', 'body' => 'Numbered steps for a process.'],
                    ['title' => 'Horizontal', 'body' => 'Side by side on wide screens.'],
                    ['title' => 'Or vertical', 'body' => 'As in the guides.'],
                ]]],
            ], 'white', 'lg', 'wide'),
            B::band([
                B::heading('Media', null, null, 'md'),
                B::spacer('sm'),
                ['type' => 'gallery', 'data' => ['columns' => '4', 'layout' => 'grid', 'images' => ['demo/fjord.jpg', 'demo/mountains.jpg', 'demo/forest.jpg', 'demo/lake.jpg', 'demo/camp.jpg', 'demo/city.jpg', 'demo/desk.jpg', 'demo/coffee.jpg']]],
            ], 'muted', 'lg', 'wide'),
            B::band([
                B::heading('Pricing', null, null, 'md'),
                B::spacer('sm'),
                B::grid([
                    $this->plan('Starter', '0', 'For trying things out.', "One site\nThe visual editor\nCommunity support", false),
                    $this->plan('Team', '49', 'For a content team.', "Unlimited people\nThe AI assistant and MCP\nSeveral languages", true),
                    $this->plan('Agency', '199', 'For many client sites.', "Unlimited sites\nSingle sign-on\nPriority support", false),
                ], 3),
            ], 'white', 'lg', 'wide'),
            B::band([
                B::heading('Team and social proof', null, null, 'md'),
                B::spacer('sm'),
                B::columns([
                    [['type' => 'team-member', 'data' => ['name' => 'Thomas Alrek', 'role' => 'Creator of Aurora', 'photo' => 'demo/thomas.jpg', 'bio' => 'Builds Aurora and writes these docs.']]],
                    [B::testimonial('I wanted a CMS where editors work on the page itself and developers keep plain Laravel. Aurora is that CMS.', 'Thomas Alrek', 'Creator of Aurora', 'demo/thomas.jpg', 'card')],
                ], '1-2', 'lg', 'stretch'),
            ], 'muted', 'lg', 'wide'),
            B::band([
                ['type' => 'cta', 'data' => ['style' => 'brand', 'title' => 'Call to action', 'text' => 'Four styles: aurora, brand, dark and light.', 'button_label' => 'Back to the homepage', 'button_type' => 'page', 'button_page' => $this->id('home')]],
            ], 'white', 'md', 'wide'),
        ];
    }

    /**
     * An example pricing plan for the component library.
     */
    protected function plan(string $name, string $price, string $description, string $features, bool $featured): array
    {
        return ['type' => 'pricing', 'data' => [
            'name' => $name, 'price' => $price, 'currency' => '€', 'period' => '/month', 'description' => $description,
            'features' => array_map(fn (string $text): array => ['text' => $text], explode("\n", $features)), 'featured' => $featured, 'badge' => $featured ? 'Most popular' : '',
            'button_label' => 'Choose '.$name, 'button_type' => 'page', 'button_page' => $this->id('contact'),
        ]];
    }

    protected function blocksForContact(bool $officeHours = false): array
    {
        return [
            B::band([
                B::columns([
                    [
                        B::heading('Talk to us', 'Contact', 'Questions about Aurora, a demo for your team, or help moving from another CMS: tell us what you need and we will get back to you within a working day.', 'xl', 'left', 'h1'),
                        ['type' => 'list', 'data' => ['marker' => 'check', 'items' => "A walkthrough of the editor for your team\nAdvice on moving your content across\nHelp writing your first blocks"]],
                        ...($officeHours ? [B::text('We answer on weekdays, 9–16 CET.', 'lead')] : []),
                    ],
                    [['type' => 'form', 'data' => ['form_id' => $this->contactForm?->id, 'style' => 'card']]],
                ], '1-1', 'xl', 'start'),
            ], 'white', 'xl', 'wide'),
        ];
    }
}
