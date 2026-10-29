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
                'badge_text' => 'Aurora 1.5 is out',
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
                B::heading('Aurora 1.5', 'October 2026', 'Page templates: pages new pages start from, designed in the visual editor and offered in New page.', 'md', 'left', 'h2'),
                B::rich('<h3>Upgrading from 1.4</h3><p>One migration adds the page templates\' columns to the pages table; until it has run, the site works as before, without templates. Then create the new permission, and give it to the people who design templates under Users &amp; roles.</p>'),
                B::code("composer update shipaurora/cms\nphp artisan migrate                 # pages.is_template and pages.template_id\nphp artisan aurora:sync-permissions  # \"Manage page templates\"\nphp artisan boost:update             # the updated Aurora skills", 'bash'),
                B::rich('<h3>What\'s new</h3><ul><li><strong>1.5.1</strong>: <em>Create template from this page</em> (it was <em>Save as page template</em>) is in the page list\'s row menu too. <em>Blank</em> in <em>New page</em> is drawn as an empty page in the site\'s default layout. The visual editor offers <em>Publish</em> only when a live page has changes waiting, and never on a template. A template and a page with the same title keep the same address, and neither changes the other\'s. The AI assistant and MCP apps build pages from the site\'s blocks, and ask before adding custom HTML.</li><li><strong>Page templates</strong>: a page template is a layout and blocks, ready to fill in, that new pages can start from. <em>New page</em> offers <em>Blank</em> and the site\'s templates, and the new page gets a copy of the template\'s layout, the layout\'s fields, its blocks and their translations. Templates are made under Settings › Templates › Page templates or from a page (<em>Save as page template</em>), designed in the visual editor, and never on the website. See <em>Pages</em>.</li><li><strong>A permission</strong>, <em>Manage page templates</em>, for making, changing and deleting them; no role has it until it\'s given.</li><li><strong>Start from</strong> another page now keeps the blocks\' translations too.</li><li><strong>For developers</strong>: templates are <code>Aurora\\Models\\PageTemplate</code>, in the pages table, and <code>Page</code> never returns one; <code>Aurora\\Support\\PageTemplates</code> makes them and copies their content.</li></ul>'),
                $this->seeAlso(['pages' => 'Pages', 'visual-editor' => 'The visual editor']),
            ], 'white', 'lg', 'narrow'),
            B::band([
                B::heading('Aurora 1.4', 'October 2026', 'Pages found by their whole address, redirects that never go round in a circle, and permissions and AI tools of a website\'s own.', 'md', 'left', 'h2'),
                B::rich('<h3>Upgrading from 1.3</h3><p>There are no migrations. Update the CMS and the skills your AI coding assistant reads. A website that registers permissions of its own creates them with <code>aurora:sync-permissions</code>, after every deploy that adds some.</p>'),
                B::code("composer update shipaurora/cms\nphp artisan boost:update             # the updated Aurora skills\nphp artisan aurora:sync-permissions  # the website's own permissions, if it has any", 'bash'),
                B::rich('<h3>What\'s new</h3><ul><li><strong>Addresses</strong>: a page is found by its whole address, part by part, so pages in different places may share a slug (<code>/services/design</code> and <code>/work/design</code>). Only pages beside each other need different ones. See <em>Pages</em>.</li><li><strong>Redirects never go round in a circle</strong>: one that would send visitors back to an address on their way isn\'t saved, and one that comes to loop later isn\'t followed. See <em>Menus, redirects and trash</em>.</li><li><strong>Permissions and policies</strong>: <code>Permissions::register()</code> adds a website\'s own permissions to Users &amp; roles, for its policies to check; <code>aurora:sync-permissions</code> creates them on a running site. See <em>Your own screens and models</em>.</li><li><strong>AI tools of your own</strong>: <code>McpTools::register()</code> adds a website\'s own tools to the MCP server and the assistant in the admin. AI apps now name a page by its id or its whole address.</li></ul><h3>What may look different</h3><p>Code that looked a page up by its slug alone needs a look; nothing else does.</p><table><tr><th>What</th><th>Before</th><th>Now</th></tr><tr><td>Two pages with the same slug</td><td>Not allowed anywhere on a site</td><td>Allowed under different parents; still not beside each other</td></tr><tr><td>An old address ending in a slug several pages share</td><td>Sent on to one of them</td><td>"Page not found", unless a redirect says where it goes</td></tr><tr><td>A page named by its slug to an AI app</td><td>The first page with that slug</td><td>The page with that id or whole address; a slug several pages share is asked about</td></tr><tr><td>A redirect that loops</td><td>Saved, and visitors sent round and round</td><td>Refused when saved; one that loops later answers "Page not found"</td></tr><tr><td><code>Page::query()->whereSlug()</code> in a website\'s own code</td><td>One page</td><td>Possibly several: use <code>Page::findByPath()</code></td></tr></table>'),
                $this->seeAlso(['pages' => 'Pages', 'menus-redirects-trash' => 'Menus, redirects and trash', 'website-screens' => 'Your own screens and models']),
            ], 'white', 'lg', 'narrow'),
            B::band([
                B::heading('Aurora 1.3', 'October 2026', 'Block screenshots from the site\'s own files, made from each block\'s example data, and skills for AI coding assistants that cover a whole website, including moving an existing one onto Aurora. No breaking API changes.', 'md', 'left', 'h2'),
                B::rich('<h3>Upgrading from 1.2</h3><p>There are no migrations. Update the CMS, then the skills your AI coding assistant reads (the starter app does this on every <code>composer update</code>).</p>'),
                B::code("composer update shipaurora/cms\nphp artisan boost:update   # the new and updated Aurora skills", 'bash'),
                B::rich('<h3>What\'s new</h3><ul><li><strong>1.3.17</strong>: the AI skills for porting a site keep every page at the address it had, nested addresses included: parent pages are made first and their children placed under them, and two pages that would share a slug get another one and a redirect.</li><li><strong>1.3.16</strong>: the AI skills for porting a site keep its icon set (Font Awesome as Blade Font Awesome, never swapped for Aurora\'s own icons), copy its keys over into <code>.env</code> or Settings by what each is (on Netflex, from its API\'s variables too), and remove the starter app\'s Welcome example once the site\'s own homepage is there.</li><li><strong>1.3.15</strong>: a website can give its settings one item in Settings\' side menu, with a tab per section: <code>SettingsTabs::page()</code>, which its tabs join with <code>page:</code>. And an API key or token editors enter can go in a <code>SecretInput</code> (<code>secretKeys</code>): stored encrypted, never sent to the browser, read with <code>SettingSecrets::get()</code>. See <em>Your own settings</em>. The AI skills for porting a site now bring its error pages and image sizes across too, and end by comparing the old site and the new, page by page.</li><li><strong>1.3.14</strong>: for a site with a shop, the skills for AI coding assistants recommend Shopify headless: the product pages, collections and cart are served by the website from Shopify\'s Storefront API, and only the checkout is Shopify\'s, on a subdomain of the site\'s own. They offer blocks linked to Shopify\'s data (product cards, product grids and carousels from a collection, hand-picked and featured products, collection lists, an add-to-cart button), picked from the products in Aurora\'s admin and showing Shopify\'s prices and stock in the language being shown.</li><li><strong>1.3.13</strong>: a website or package can add Filament plugins to the admin without changing the starter app: <code>AdminPanel::plugins([...])</code> in a service provider\'s <code>register()</code>. The resources they bring show every site\'s records unless registered with <code>perSite: true</code>. See <em>Your own screens and models</em>. And the AI skills for porting a site now say what to offer when it has a shop, a customer register or newsletters, which Aurora doesn\'t have.</li><li><strong>1.3.12</strong>: a new form shows its field builder again: it was hidden while creating a form, so saving a new one seemed to do nothing. And a file used by a website\'s own record, such as an event, is named under <em>Used on</em>, with the kind of record and a link to open it.</li><li><strong>1.3.11</strong>: a website can put its own Filament widgets, or a package\'s, on the dashboard: <code>AdminPanel::widgets([...])</code> in a service provider\'s <code>register()</code>, or <code>AdminPanel::discover()</code>, which finds widgets too. People add them under <em>Customise dashboard</em>. See <em>Your own screens and models</em>. And a website or package can add its own form field types, such as a captcha or an address picker: a class extending <code>Aurora\\Forms\\FieldType</code>, registered with <code>FormFields::register()</code>. Aurora\'s own ten types are such classes now too; forms and submissions are unchanged. See <em>Forms</em>.</li><li><strong>1.3.10</strong>: the welcome card fits every size it can have on the dashboard: small, its shortcuts sit in two columns under the greeting; medium, in a row under it; large, beside it.</li><li><strong>1.3.9</strong>: a saved dashboard is kept on sites with their own <code>App\\Models\\User</code>, such as those made from the starter app: before, it went back to the default layout on reload, and an admin theme picked in the profile wasn\'t saved. Customising now removes a widget with ×, and <em>Add a widget</em> adds any widget, as often as you like.</li><li><strong>1.3.8</strong>: each person can lay out their own dashboard: <em>Customise dashboard</em> turns the widgets into cards to drag into another order, make small, medium or large, hide or add back, and only their own dashboard changes. Each person can also pick their own admin theme under <em>Admin theme</em> in their profile; without one, they see the website\'s theme from Settings › Appearance. The media library has a <em>Credit</em> filter that finds every file whose credit contains a name. Run <code>php artisan migrate</code> after updating: the people table gets a column for each person\'s dashboard and admin theme.</li><li><strong>1.3.7</strong>: the AI assistant can tag images. Switch on <em>Tag images automatically</em> under Settings › AI Assistant and each image added to the media library gets a few tags, reusing the library\'s tags where they fit; switching it on also tags the images that have none. <em>Tag images</em> in the media library\'s bulk actions does the same for selected images, with an optional instruction. Tags someone chose are never changed.</li><li><strong>1.3.6</strong>: a new skill for AI coding assistants, <code>aurora-netflex</code>, ports a website built on Netflex: it rebuilds the site as a new Aurora site, works out each block\'s fields from the old components\' editor buttons, and brings the pages, blocks, structures, files, labels, menus and redirects across from the Netflex API. See the guide <em>Move a Netflex site to Aurora</em>.</li><li><strong>1.3.5</strong>: the skills and the docs say where a website registers its admin screens and menu panels: <code>AdminPanel</code> and <code>Menus::panels()</code> in a service provider\'s <code>register()</code> method, not <code>boot()</code>, which runs after Aurora\'s admin panel is built, so screens added there never showed.</li><li><strong>1.3.4</strong>: Aurora can be installed beside Statamic, to move a Statamic site onto it in its own project. Aurora\'s pages are now the last routes to be matched, so the routes of a package registered after Aurora win, such as Statamic\'s control panel at <code>/cp</code>, and its website until its routes are turned off. A new skill for AI coding assistants, <code>aurora-statamic</code>, does the port when asked to: see the guide <em>Move a Statamic site to Aurora</em>.</li><li><strong>1.3.3</strong>: the <code>aurora-development</code> skill shows AI coding assistants how blocks follow the brand and accent colours editors choose under Settings › Website › Branding: the site\'s defaults as <code>--color-brand-*</code> and <code>--color-accent-*</code> in plain <code>@theme</code> (not <code>inline</code>), blocks painted with <code>brand-*</code> and <code>accent-*</code> instead of fixed colours, and <code>Fields::tone()</code> for a colour per block. The starter app 1.2.7 has Aurora\'s colours in its stylesheet, so a new site\'s blocks follow the admin\'s from the start.</li><li><strong>1.3.2</strong>: until a block has a screenshot, the block list shows the block itself, live from its sample page and shrunk to its card, instead of no picture. A screenshot, uploaded or in <code>resources/block-previews</code>, still comes first, and is lighter to load.</li><li><strong>1.3.1</strong>: ask an AI coding assistant to <em>generate the missing block screenshots</em> and it can. <code>php artisan aurora:block-previews --missing --json</code> lists the blocks without one and their sample pages; the assistant screenshots each with its own browser and saves it with <code>php artisan aurora:block-previews hero --from=hero.png</code>, which makes it the block list\'s size (800 × 450, WebP) in <code>resources/block-previews</code>, ready to commit. With <code>spatie/browsershot</code> installed (optional), <code>--generate</code> takes them itself, and AI apps connected over MCP get a <code>generate_block_screenshots</code> tool that saves them as if uploaded in Settings › Blocks. <code>BlockScreenshots::using()</code> tells Browsershot where Chrome is.</li><li><strong>Block screenshots</strong>: save a screenshot of a block as <code>resources/block-previews/{type}.webp</code> (or <code>.png</code>, <code>.jpg</code>) and it is the block\'s picture in the visual editor\'s block list, with no code of the site\'s own. <code>php artisan aurora:block-previews</code> (<code>--missing</code> for the blocks without one) lists the page to take each from: the block alone, drawn from its example data on the blank layout with the site\'s stylesheet, at a signed address a screenshot tool can open without signing in. <code>make:aurora-block</code> says where the screenshot goes.</li><li><strong>Example data</strong>: <code>Block::sample()</code> is the block as its screenshot shows it, its <code>defaults()</code> by default; a block that holds others adds the blocks in it, so a grid is shown full rather than empty.</li><li><strong>Skills for AI coding assistants</strong> (installed with Laravel Boost): <code>aurora-development</code> now covers block screenshots, Aurora\'s fields for the inspector (icons, colours, links, media library images, pages, the website\'s own records), what a layout draws around the blocks (its head, menus, an edit link), website routes and how a form block works with Aurora. A new <code>aurora-porting</code> skill shows how to move a website onto Aurora: what becomes a layout, a block, a setting or a model of the site\'s own, worked through with an <code>Employee</code> model, its admin screen and a block that picks one, and how the content, files, menus and old addresses come across.</li></ul><h3>What may look different</h3><table><tr><th>What</th><th>Before</th><th>Now</th></tr><tr><td>A block\'s picture in the block list</td><td>None, unless the block\'s <code>preview()</code> gave one</td><td>The site\'s screenshot in <code>resources/block-previews</code>, when there is one. A block\'s own <code>preview()</code> and a screenshot uploaded in Settings › Blocks still come first</td></tr></table>'),
                $this->seeAlso(['creating-blocks' => 'Creating blocks', 'ai' => 'AI: MCP, the assistant and Write with AI']),
            ], 'white', 'lg', 'narrow'),
            B::band([
                B::heading('Aurora 1.2', 'October 2026', 'Rich text on the canvas, layouts in code, an SEO audit that reads the page as it\'s drawn, shortcodes of your own, and an API for a website\'s own screens, routes and imports. No breaking API changes.', 'md', 'left', 'h2'),
                B::rich('<h3>Upgrading from 1.0 or 1.1</h3><p>Until the migrations have run, the site works as before, without the new features.</p>'),
                B::code("composer update shipaurora/cms\nphp artisan migrate          # pages.settings (1.1) and layouts.class (1.2)\nphp artisan filament:assets   # the canvas's new rich text script", 'bash'),
                B::rich('<h3>What\'s new</h3><ul><li><strong>1.2.11</strong>: under <em>AI › Connect AI apps</em>, the commands and settings to copy name the server after the website (<code>APP_NAME</code>): <code>apriil-digital</code> for "Apriil Digital", instead of <code>aurora-cms</code> on every site, so several Aurora sites can be added to one AI app. Apps already connected keep their name.</li><li><strong>1.2.10</strong>: on Filament 5.11, the dashboard\'s widgets are no longer squeezed into one column each. Aurora now needs Filament 5.11 and the Borealis or Minimal theme 1.0.12, rebuilt for it, so <code>composer update</code> brings all three.</li><li><strong>1.2.9</strong>: with folders among the files, a folder that holds folders but no files of its own no longer says it has no files under them. The themes 1.0.11 end the table with the folders, inside its rounded corners.</li><li><strong>1.2.8</strong>: an admin theme\'s stylesheet gets a new address with each release of the theme (<code>borealis.css?v=1.0.10</code>), so browsers and caches load an updated theme straight away instead of keeping the old one for hours.</li><li><strong>1.2.7</strong>: the open folder\'s dropdown lists only the folders next to it, and the folder dropdowns are as wide as their longest name. Aurora now needs the Borealis or Minimal theme 1.0.9 or later, so Composer updates the theme along with the CMS; on a site whose Filament is held back, run <code>composer update -W</code>.</li><li><strong>1.2.6</strong>: in the media library\'s folder trail, only <em>Library</em> and the open folder open the folder tree; the folders between them open with a click.</li><li><strong>1.2.5</strong>: the media library shows a folder\'s folders among its files, as the first cards of the grid or the first rows of the list; <em>Settings › Media › Media library</em> puts them back in a row of their own. The list has no heading row any more and sorts from the toolbar. A photo added to the library gets its credit from its EXIF Artist, or Copyright. The Borealis and Minimal themes 1.0.9 draw the folders as cards. The starter app no longer ships a <code>composer.lock</code>, so <code>composer create-project</code> gets the newest Aurora.</li><li><strong>1.2.4</strong>: the browser\'s back and forward buttons move between the folders opened in the media library.</li><li><strong>1.2.3</strong>: the media library\'s bulk actions set or clear the credit of the selected files and, with the AI assistant set up, write alt text for the selected images that have none, with an optional instruction. Inside a folder, an arrow beside its name opens the folder above.</li><li><strong>1.2.2</strong>: <code>&lt;x-aurora::layout&gt;</code> draws a website route\'s own content in one of the site\'s layouts, and <code>Layouts::wrap()</code> does the same from a controller. Layout classes in <code>app/Layouts</code> are found on their own, and <code>make:aurora-layout</code> now makes a layout in code there. Links to subpages from cards and buttons go to the whole address (<code>/docs/get-started/installation</code>, not <code>/installation</code>).</li><li><strong>1.2.1</strong>: the <code>@editableItem</code> directive makes text in a repeater\'s items editable on the canvas. Views that call <code>InlineEditing::itemAttributes()</code> keep working.</li><li><strong>Visual editor</strong>: rich text is edited right on the canvas, for fields a block declares <code>Editable::richText()</code>: bold, italic, underline, strikethrough, links, H2 and H3, lists and quotes. Text with tables, images or alignment stays in the inspector.</li><li><strong>Layouts in code</strong>: a class extending <code>Aurora\Layouts\PageLayout</code>, registered with <code>Layouts::register()</code>. It\'s listed with the admin\'s layouts and can be the default, but can\'t be edited or deleted there. <em>Make default</em> works for every layout.</li><li><strong>SEO</strong>: the audit reads the page as drawn. New warnings for more than one H1 anywhere on the page, links that lead nowhere, links without text and images without an alt attribute; new suggestions for skipped heading levels, links through a redirect, short titles and pages under 300 words.</li><li><strong>Pages list</strong>: sorted by address, the homepage first and each page followed by its subpages.</li><li><strong>Shortcodes</strong>: editors write their own under Settings › Website › Shortcodes.</li><li><strong>Canvas styles</strong>: combinators are kept, the page\'s colours carry over, and width breakpoints and <code>vw</code> follow the canvas.</li><li><strong>Translations</strong>: <em>Same in …</em> marks text that doesn\'t change in a language as translated, and a site\'s own <code>lang/*.json</code> labels beat Aurora\'s without importing.</li><li><strong>Blocks API</strong>: <code>current_page()</code>, <code>block_depth()</code> and <code>@blockDepth</code>, <code>is_nested_block()</code> and <code>@nested</code>, <code>Blocks::forget()</code> for the page builder\'s own blocks, and <code>&lt;x-aurora::blocks&gt;</code> takes attributes and <code>:blocks</code>.</li><li><strong>Website API</strong>: <code>AdminPanel</code> for the website\'s own screens and navigation groups, <code>HasPublishing</code> and <code>Publishing</code> to publish models like pages, <code>Menus</code> for menu panels and links in every language, <code>Sitemap::add()</code>, and <code>AssetLibrary::fromFile()</code> taking an id for imports that can run again.</li></ul><h3>What may look different</h3><p>No code needs changing, but these can make a site look or read differently after updating.</p><table><tr><th>What</th><th>Before</th><th>Now</th></tr><tr><td><code>body &gt; .x</code> in the site\'s CSS, on the canvas</td><td>Matched any <code>.x</code> on the canvas</td><td>Matches only an <code>.x</code> straight on the canvas, as on the website</td></tr><tr><td>Width media queries on the canvas</td><td>Asked of the admin window</td><td>Asked of the canvas; <code>vw</code> is the canvas\'s width. Tablet and Mobile show the site\'s tablet and mobile layouts</td></tr><tr><td>The canvas\'s background and text colour</td><td>The editor\'s white won over colours in a CSS layer</td><td>The page\'s <code>html</code> and <code>body</code> colours</td></tr><tr><td>Admin labels</td><td>Imported labels beat the app\'s <code>lang/*.json</code></td><td>Only labels edited in the admin beat the app\'s files, and the app\'s files beat Aurora\'s</td></tr><tr><td>Lists of selected records (a repeater of selects)</td><td>Offered for translation</td><td>The same in every language; translations already stored for them are ignored</td></tr><tr><td>More than one H1</td><td>A suggestion, counting hero and heading blocks</td><td>A warning, counting the whole page as drawn, so it shows in the SEO report</td></tr><tr><td>The pages list\'s order</td><td>Last edited first</td><td>By address</td></tr><tr><td>The media library\'s folders (1.2.5)</td><td>A row of their own above the files</td><td>The first cards of the grid, or rows of the list. <em>Settings › Media › Media library</em> switches it back</td></tr><tr><td>The media library\'s list (1.2.5)</td><td>A table with column headings to sort by</td><td>A list without headings, sorted from the toolbar</td></tr></table>'),
                $this->seeAlso(['canvas-styles' => 'The canvas shows your CSS', 'layouts' => 'Layouts', 'seo' => 'SEO checks', 'website-screens' => 'Your own screens and models']),
            ], 'white', 'lg', 'narrow'),
            B::band([
                B::heading('Aurora 1.1', 'October 2026', 'Fields for layouts, and upload limits of your own.', 'md', 'left', 'h2'),
                B::rich('<ul><li><strong>Layouts</strong>: layouts can have fields of their own, such as a background image, registered with <code>Layouts::schema()</code>. They show under the layout picker, are saved in the new <code>pages.settings</code> column by view, and reach the view as <code>$settings</code>. AI tools read and set them. Needs the migration.</li><li><strong>Media</strong>: upload limits are configurable, with <code>aurora.media.max_image_mb</code> (10) and <code>max_file_mb</code> (50), or <code>AURORA_MAX_IMAGE_MB</code> and <code>AURORA_MAX_FILE_MB</code>. Files between 10 and 50 MB no longer fail.</li><li><strong>Appearance</strong>: each admin theme shows its version under Settings › Appearance.</li></ul>'),
            ], 'white', 'lg', 'narrow'),
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
