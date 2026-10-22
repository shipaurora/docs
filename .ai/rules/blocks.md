---
paths:
  - 'app/Blocks/**'
  - 'resources/views/components/blocks/**'
---

# Blocks

## Page blocks: class, Blade component, registration
Create blocks with `php artisan aurora:make-block`. Each extends `Aurora\Blocks\Block` in app/Blocks (namespace `App\Blocks`) and renders through a matching anonymous component at `resources/views/components/blocks/{type}.blade.php`. They're discovered from `aurora.block_path`; `App\Providers\BlocksServiceProvider` lists them in palette order. Register blocks only through `Aurora\Blocks\Blocks` (never with the page builder package directly). The CMS's tests use a copy of these blocks (its workbench/): copy a changed block there too.

## Block views use the global helpers
Render block fields with Aurora's global helpers (`inline_text()`, `plain_text()`, `rich_text()`, `shows_field()`, `link_href()`, `link_target()`, `is_editing()`, `block_slot()`) rather than importing `Markup`, `Fields` or `Blocks` in `@php`, and images with `<x-aurora::media>`.

## Blocks are styled by the website's stylesheet alone
Block styles live in resources/css/app.css, which the visual editor's canvas also loads, scoped to it. Style the page itself (font, colour, background, fonts to load) in that stylesheet through `:root`, `html` and `body`, never with classes on `<body>` or `<link>`s in the layout's head, so the canvas gets it too.
