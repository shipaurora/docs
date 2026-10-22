<img src="public/brand-mark.svg" alt="Aurora" width="80" height="80">

# shipaurora.com

[![Website](https://img.shields.io/website?url=https%3A%2F%2Fshipaurora.com&label=shipaurora.com)](https://shipaurora.com)
[![Tests](https://github.com/shipaurora/docs/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/shipaurora/docs/actions/workflows/tests.yml)
[![Aurora](https://img.shields.io/packagist/v/shipaurora/cms?label=Aurora)](https://packagist.org/packages/shipaurora/cms)
[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Filament 5](https://img.shields.io/badge/Filament-5-FDAE4B)](https://filamentphp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue)](LICENSE)

**The website of [Aurora](https://github.com/shipaurora/cms)**, a content management system for
Laravel built on Filament: its documentation and its demo, at [shipaurora.com](https://shipaurora.com).
It's a website built on Aurora itself, installed from Packagist like any other: its own page
layout, blocks and settings tabs, with every page of the documentation seeded from code.

## What's in it

- `app/Blocks` and `resources/views/components/blocks`: the website's blocks.
- `resources/views`: its layout, header, footer and edit bar.
- `database/seeders/Demo`: its content. The documentation pages, menus, settings and translations
  are seeded, and seeded again on every deploy, so change them here, not in the admin.
- `tests`: tests of what this website adds. Aurora's own tests are in the
  [cms](https://github.com/shipaurora/cms) repository.

## Run it locally

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan make:aurora-user   # an account to sign in to /admin with
```

## License

MIT, as Aurora is. See [LICENSE](LICENSE).
