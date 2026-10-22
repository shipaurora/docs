---
paths:
  - '**'
---

# The docs website

## A website built on Aurora, not the CMS
This app is shipaurora.com: Aurora's docs and demo, a website built on the CMS (shipaurora/cms) and its themes, installed from Packagist like any other site. It owns its page templates (resources/views), its blocks (app/Blocks), its header and footer settings tabs (`App\Providers\BlocksServiceProvider`) and its content (the seeders in database/seeders/Demo). Never edit vendor/shipaurora: a change to the CMS is made, tested and released in the cms repository, then installed here with `composer update "shipaurora/*"`.

## Use the CMS's hooks and helpers
Extend the CMS only through what it offers websites: `Aurora\Blocks\Blocks::register()`, `Aurora\Support\SettingsTabs::register()`, the `aurora` config, the published views in resources/views/vendor/aurora, and its models and helpers (`CurrentSite`, `ContentLocale`, `Setting::get()`).
