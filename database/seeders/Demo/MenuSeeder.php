<?php

namespace Database\Seeders\Demo;

use Aurora\Models\Menu;
use Aurora\Models\MenuItem;
use Aurora\Models\NavigationLocation;
use Aurora\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $pages = Page::query()->get()->mapWithKeys(fn (Page $page): array => [$page->slug => $page->id]);

        $this->menu('Main navigation', 'header', [
            ['page' => 'features', 'children' => [
                ['title' => ['en' => 'All features', 'nb' => 'Alle funksjoner'], 'page' => 'features', 'icon' => 'hero-o-squares-2x2'],
                ['page' => 'why-aurora', 'icon' => 'hero-o-light-bulb'],
                ['page' => 'components', 'icon' => 'hero-o-swatch'],
            ]],
            ['page' => 'docs', 'children' => [
                ['page' => 'installation', 'icon' => 'hero-o-rocket-launch'],
                ['page' => 'visual-editor', 'icon' => 'hero-o-squares-plus'],
                ['page' => 'creating-blocks', 'icon' => 'hero-o-code-bracket'],
                ['page' => 'ai', 'icon' => 'hero-o-sparkles'],
                ['page' => 'headless-apis', 'icon' => 'hero-o-cloud'],
                ['title' => ['en' => 'All docs', 'nb' => 'All dokumentasjon'], 'page' => 'docs', 'icon' => 'hero-o-book-open'],
            ]],
            ['page' => 'guides'],
            ['page' => 'release-notes'],
        ], $pages);

        // The docs layout's sidebar (resources/views/layouts/docs.blade.php): each section and
        // its pages, in reading order. Editors reorder the docs by dragging these items.
        NavigationLocation::query()->updateOrCreate(['handle' => 'docs'], ['name' => 'Docs sidebar', 'description' => 'Beside every page in the Docs layout.', 'sort' => 4]);

        $this->menu('Docs', 'docs', array_map(
            fn (string $section, array $docs): array => ['page' => $section, 'children' => array_map(fn (string $slug): array => ['page' => $slug], array_keys($docs[2]))],
            array_keys(PageSeeder::DOCS),
            PageSeeder::DOCS,
        ), $pages);

        $this->menu('Footer', 'footer', [
            ['title' => ['en' => 'Product', 'nb' => 'Produkt'], 'children' => [['page' => 'features'], ['page' => 'why-aurora'], ['page' => 'components'], ['page' => 'release-notes']]],
            ['title' => ['en' => 'Learn', 'nb' => 'Lær'], 'children' => [['page' => 'docs'], ['page' => 'installation'], ['page' => 'guides'], ['page' => 'reference']]],
            ['title' => ['en' => 'Elsewhere', 'nb' => 'Andre steder'], 'children' => [['page' => 'contact'], ['title' => ['en' => 'GitHub', 'nb' => 'GitHub'], 'url' => 'https://github.com/shipaurora', 'target' => '_blank']]],
        ], $pages);

        // The small links at the very bottom of this website's footer.
        NavigationLocation::query()->updateOrCreate(['handle' => 'legal'], ['name' => 'Footer: legal links', 'description' => 'The small links at the very bottom.', 'sort' => 3]);

        $this->menu('Legal', 'legal', [
            ['page' => 'privacy'],
            ['page' => 'terms'],
            ['title' => ['en' => 'Sitemap', 'nb' => 'Nettstedskart'], 'url' => '/sitemap.xml'],
        ], $pages);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function menu(string $name, string $location, array $items, $pages): void
    {
        $menu = Menu::query()->create(['name' => $name, 'is_visible' => true]);
        $menu->locations()->create(['location' => $location]);

        $this->items($menu, $items, null, $pages);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function items(Menu $menu, array $items, ?string $parentId, $pages): void
    {
        foreach ($items as $order => $item) {
            $page = isset($item['page']) ? Page::query()->find($pages[$item['page']]) : null;

            $model = MenuItem::query()->create([
                'menu_id' => $menu->id,
                'parent_id' => $parentId,
                // Its own wording in English and Norwegian; a page's item has none, and shows the
                // page's title in each language (Aurora\Models\MenuItem).
                'title' => $item['title'] ?? [],
                'url' => $page ? null : ($item['url'] ?? null),
                'linkable_type' => $page?->getMorphClass(),
                'linkable_id' => $page?->getKey(),
                'icon' => $item['icon'] ?? null,
                'target' => $item['target'] ?? '_self',
                'order' => $order + 1,
            ]);

            if (! empty($item['children'])) {
                $this->items($menu, $item['children'], $model->id, $pages);
            }
        }
    }
}
