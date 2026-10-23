<?php

namespace Database\Seeders\Demo\Pages;

use Database\Seeders\Demo\Support\B;

/**
 * The guides: real tasks, step by step.
 */
trait GuidePages
{
    protected function blocksForGuides(): array
    {
        return [
            B::band([
                B::heading('Guides', 'Learn by doing', 'Short, practical how-tos for editors and developers. Each one takes about ten minutes.', 'xl', 'left', 'h1'),
            ], 'brand-soft', 'lg', 'wide'),
            B::band([
                ['type' => 'page-list', 'data' => ['source' => 'selected', 'page_ids' => [$this->id('build-a-landing-page'), $this->id('let-claude-draft-a-page'), $this->id('add-your-own-block'), $this->id('run-several-sites')], 'link_label' => 'Read the guide']],
            ], 'white', 'xl', 'wide'),
            B::band([
                ['type' => 'cta', 'data' => ['style' => 'light', 'title' => 'Looking for the reference?', 'text' => 'The docs explain every part of Aurora, in order.', 'button_label' => 'Open the docs', 'button_type' => 'page', 'button_page' => $this->id('docs')]],
            ], 'muted', 'lg', 'wide'),
        ];
    }

    protected function blocksForBuildALandingPage(): array
    {
        return $this->guide('build-a-landing-page', 'Build a landing page in the *visual editor*', 'demo/screen-editor.jpg', [
            B::rich('<p>This guide takes a campaign page from an empty canvas to published, using only the visual editor. You need a role that can create pages: an Editor, or an Author who sends it for review.</p>'),
            ['type' => 'steps', 'data' => ['layout' => 'vertical', 'items' => [
                ['title' => 'Create the page', 'body' => 'Under Pages, choose New page and give it a title. The address is made from the title. Pick the Landing page layout under Layout; Start from copies another page\'s blocks if you want a head start.'],
                ['title' => 'Open the visual editor', 'body' => 'Choose Design. The canvas shows the page in your website\'s own stylesheet, exactly as visitors will see it.'],
                ['title' => 'Add a hero', 'body' => 'Drag a Hero block from the palette onto the canvas, or click it to add it at the end. Click the headline and type. Wrap a word in *asterisks* to give it the highlight.'],
                ['title' => 'Build a section', 'body' => 'Drop a Section band below it, add a Section with two columns inside, and drag an Image into one column and a Heading and a Button group into the other. Click the empty image to pick one from the media library.'],
                ['title' => 'Fine-tune', 'body' => 'Select a block to see its fields in the inspector, with padding, background, width and alignment under design. Copy a block\'s style onto another, and undo anything with Ctrl+Z.'],
                ['title' => 'Check every width', 'body' => 'Switch between desktop, tablet and mobile in the toolbar, then Preview to see the unsaved page inside the real website, with its header and footer.'],
                ['title' => 'Publish, or send for review', 'body' => 'Save, then publish, or set a publish date to schedule it. Authors send it for review instead, and editors are notified.'],
            ]]],
            ['type' => 'alert', 'data' => ['variant' => 'info', 'title' => 'Changed your mind?', 'body' => 'Every save is a revision. Open the page\'s history to preview an earlier version and restore it in one click.']],
            B::buttons([B::button('Read: the visual editor', $this->id('visual-editor'), 'outline', icon: 'book-open')]),
        ]);
    }

    protected function blocksForLetClaudeDraftAPage(): array
    {
        return $this->guide('let-claude-draft-a-page', 'Let *Claude* draft a page for you', 'demo/screen-connect-ai.jpg', [
            B::rich('<p>Connect Claude to Aurora over MCP and ask it for a first draft. It works as you, with your permissions, and everything it does is a revision you can undo.</p><h2>1. Connect</h2><p>Open <strong>AI › Connect AI apps</strong> in the admin. It has the steps for Claude Desktop and claude.ai (a custom connector), Claude Code, VS Code and Cursor, with the address to copy. In Claude, add a custom connector with that address; Claude opens Aurora\'s sign-in, you sign in and approve.</p>'),
            B::code('https://your-site.example/mcp', 'text'),
            B::rich('<h2>2. Ask for a draft</h2><p>Be specific about the audience, the sections you want and the tone. Claude reads the block catalogue first (<code>list_block_types</code>), so it knows what it can build with and how each block is meant to be used.</p>'),
            B::testimonial('Draft a landing page for our spring webinar series: a hero, three cards for the sessions, a short FAQ and a call to action to sign up. Keep it as a draft.', 'You', 'in Claude', null, 'card'),
            B::rich('<h2>3. Review it in the editor</h2><p>Open the page in the visual editor. Claude\'s changes are in the page\'s history under your name, marked as MCP, and every tool call is in <strong>AI › Activity log</strong>. Adjust anything, then publish. If you are an Author, Claude can only send it for review, just like you.</p>'),
            ['type' => 'alert', 'data' => ['variant' => 'brand', 'title' => 'No Claude? Use the assistant', 'body' => 'With an AI provider set up under Settings › AI Assistant, the chat bubble in the admin uses the same tools and permissions. Press Ctrl+Shift+K and ask.']],
            B::buttons([B::button('Read: AI, MCP and the assistant', $this->id('ai'), 'outline', icon: 'book-open')]),
        ]);
    }

    protected function blocksForAddYourOwnBlock(): array
    {
        return $this->guide('add-your-own-block', 'Add your *own block*', 'demo/workspace.jpg', [
            B::rich('<p>A quote with a photo of the person who said it: a small block that shows the whole pattern. You\'ll make the class and the view, and Aurora does the rest.</p><h2>1. Make it</h2>'),
            B::code('php artisan aurora:make-block "Quote with photo" --category=content --icon=phosphor-quotes', 'bash'),
            B::rich('<p>That creates <code>app/Blocks/QuoteWithPhotoBlock.php</code> and <code>resources/views/components/blocks/quote-with-photo.blade.php</code>. Blocks in <code>app/Blocks</code> are registered automatically: it\'s already in the palette.</p><h2>2. Its fields</h2>'),
            B::code(<<<'PHP'
public static function schema(): array
{
    return [
        Textarea::make('quote')->label(__('Quote'))->rows(3),
        TextInput::make('name')->label(__('Name')),
        AssetPicker::make('photo')->label(__('Photo')),
    ];
}

public static function editables(): array
{
    return [
        'quote' => Editable::text()->multiline(),
        'name' => Editable::text()->placeholder(__('Name')),
    ];
}
PHP, 'php', 'app/Blocks/QuoteWithPhotoBlock.php'),
            B::rich('<h2>3. Its view</h2><p>Render the fields with the helpers: no <code>@php use</code> imports needed.</p>'),
            B::code(<<<'BLADE'
@props(['data' => []])

<figure class="flex items-center gap-6">
    <x-aurora::media :value="$data['photo'] ?? null" size="sm" class="size-20 rounded-full object-cover" />
    <blockquote>
        <p class="text-xl" @editable('quote')>{{ plain_text($data['quote'] ?? '') }}</p>
        @shows('name', $data['name'] ?? null)
            <cite @editable('name')>{{ plain_text($data['name'] ?? '') }}</cite>
        @endshows
    </blockquote>
</figure>
BLADE, 'blade', 'resources/views/components/blocks/quote-with-photo.blade.php'),
            B::rich('<h2>4. Use it</h2><p>Open any page in the visual editor and drag it in. The canvas shows it in your website\'s own CSS. It\'s also validated when AI tools write it, described to assistants, translated, indexed for search and served by the APIs, with nothing more to do.</p>'),
            B::buttons([
                B::button('Read: creating blocks', $this->id('creating-blocks'), 'outline', icon: 'book-open'),
                B::button('Read: block views and helpers', $this->id('block-views'), 'outline', icon: 'book-open'),
            ]),
        ]);
    }

    protected function blocksForRunSeveralSites(): array
    {
        return $this->guide('run-several-sites', 'Run *several websites* from one admin', 'demo/screen-languages.jpg', [
            B::rich('<p>Aurora serves more than one website, each on its own domain, from the same admin. Pages, menus, forms, redirects and settings belong to a site; media, people and roles are shared.</p><h2>1. Add a site</h2><p>Under <strong>Settings › Languages &amp; sites › Sites</strong>, add the new site with its name, domain and languages. A site switcher appears in the sidebar, and the new site\'s admin is at <code>/admin/{site}</code>.</p><h2>2. Give people access</h2><p>Open each person under Users &amp; Roles and tick the sites they work on. They only see those sites in the switcher.</p><h2>3. Point the domain</h2><p>Point the domain at the same app. A request is answered by the site whose domain it came to; the default site answers every other domain.</p>'),
            B::buttons([B::button('Read: several websites', $this->id('several-websites'), 'outline', icon: 'book-open')]),
        ]);
    }

    /**
     * A guide: its title over an image, the steps, and the other guides.
     *
     * @param  array<int, array<string, mixed>>  $content
     */
    protected function guide(string $current, string $title, string $image, array $content): array
    {
        return [
            B::band([
                B::heading($title, 'Guide', null, 'xl', 'left', 'h1'),
                B::spacer(),
                B::image($image, '', null, '16/9', true),
            ], 'white', 'lg', 'narrow'),
            B::band($content, 'white', 'md', 'narrow'),
            B::band([
                B::heading('More guides', null, null, 'md'),
                B::spacer('sm'),
                ['type' => 'page-list', 'data' => ['source' => 'selected', 'page_ids' => array_values(array_filter([
                    $this->id('build-a-landing-page'), $this->id('let-claude-draft-a-page'), $this->id('add-your-own-block'), $this->id('run-several-sites'),
                ], fn (?string $id) => $id !== $this->ids[$current])), 'link_label' => 'Read the guide']],
            ], 'muted', 'lg', 'wide'),
        ];
    }
}
