<?php

namespace Tests\Feature;

use Aurora\Models\Layout;
use Aurora\Models\Page;
use Aurora\Support\Layouts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocsSearchTest extends TestCase
{
    use RefreshDatabase;

    protected Page $docs;

    protected Page $passkeys;

    protected function setUp(): void
    {
        parent::setUp();

        // The docs: an overview page and one page in it, on this site's docs layout. And a guide
        // about the same thing, on the standard layout: not part of the docs.
        $layout = Layouts::put(null, ['name' => 'Docs', 'handle' => 'docs', 'view' => 'layouts.docs']);

        $this->docs = Page::factory()->published()->create(['title' => 'Docs', 'slug' => 'docs', 'layout_id' => $layout->getKey()]);

        $this->passkeys = Page::factory()->published()->create([
            'title' => 'Passkeys and two-factor sign-in',
            'slug' => 'passkeys',
            'parent_id' => $this->docs->getKey(),
            'layout_id' => $layout->getKey(),
            'excerpt' => 'Sign in without a password.',
        ]);

        Page::factory()->published()->create([
            'title' => 'Passkeys for editors, a guide',
            'slug' => 'passkeys-guide',
            'layout_id' => Layout::query()->where('handle', 'standard')->value('id'),
        ]);
    }

    public function test_a_search_finds_docs_pages_and_leaves_the_rest_of_the_site_out(): void
    {
        $this->get('/docs?q=passkeys')
            ->assertOk()
            ->assertSee('One page for “passkeys”', false)
            ->assertSee('Passkeys and two-factor sign-in')
            ->assertSee('<mark>Passkeys</mark>', false)
            ->assertDontSee('Passkeys for editors, a guide');
    }

    public function test_a_page_mentioning_the_words_only_in_a_card_to_another_page_is_not_found(): void
    {
        // The next page's card, under every doc: its words are the other page's.
        Page::factory()->published()->create([
            'title' => 'Webhooks',
            'slug' => 'webhooks',
            'parent_id' => $this->docs->getKey(),
            'layout_id' => $this->docs->layout_id,
            'blocks' => [['id' => 'next', 'type' => 'card', 'data' => [
                'title' => 'Passkeys and two-factor sign-in',
                'body' => 'Sign in without a password.',
                'link_type' => 'page',
                'link_page' => $this->passkeys->getKey(),
            ]]],
        ]);

        $this->get('/docs?q=passkeys')
            ->assertSee('Passkeys and two-factor sign-in')
            ->assertDontSee('>Webhooks<', false);
    }

    public function test_a_search_without_matches_says_so(): void
    {
        $this->get('/docs?q=kangaroo')
            ->assertOk()
            ->assertSee('Nothing in the docs matches “kangaroo”', false);
    }

    public function test_without_a_search_the_docs_page_shows_its_own_content_and_the_search_box(): void
    {
        $this->get('/docs')
            ->assertOk()
            ->assertSee('role="search"', false)
            ->assertSee('action="'.$this->docs->url().'"', false)
            ->assertDontSee('Nothing in the docs matches');
    }
}
