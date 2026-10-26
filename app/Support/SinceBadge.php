<?php

namespace App\Support;

use CarlJanzell\FilamentPageBuilder\BlockRegistries;

/**
 * The [since 1.2.4] shortcode: a small badge beside a feature in the docs saying which
 * Aurora release it needs. Features that came with 1.0.0 have none. Written straight into
 * the docs' text, so a release's notes and the feature's own page can say it the same way.
 */
class SinceBadge
{
    public const NAME = 'since';

    /**
     * A shortcode as written in the text, with its version.
     */
    protected const PATTERN = '/\s*\[since\s+"?(\d+\.\d+(?:\.\d+)?)"?\s*\]/';

    /**
     * Into the page builder's shortcodes, once the admin panel has made its registry.
     */
    public static function register(): void
    {
        app()->booted(fn () => app(BlockRegistries::class)->for('admin')->shortcodes()->register(
            self::NAME,
            fn (array $attributes): string => static::html((string) ($attributes['version'] ?? $attributes[0] ?? '')),
            fn (): string => __('The Aurora release a feature needs, as a badge.'),
            '[since 1.2.4]',
        ));
    }

    /**
     * The badge for a version, or nothing for one that isn't a version number.
     */
    public static function html(string $version): string
    {
        if (! preg_match('/^\d+\.\d+(\.\d+)?$/', $version)) {
            return '';
        }

        return sprintf(
            '<span class="docs-since" title="%s">v%s</span>',
            e(__('Needs Aurora :version or newer', ['version' => $version])),
            e($version),
        );
    }

    /**
     * Text without its badges, for search: a snippet shouldn't show "[since 1.2.4]".
     */
    public static function strip(string $text): string
    {
        return preg_replace(self::PATTERN, '', $text) ?? $text;
    }
}
