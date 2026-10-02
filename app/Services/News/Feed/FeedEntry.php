<?php

namespace App\Services\News\Feed;

use Carbon\CarbonInterface;

/** One normalised entry read from an RSS or Atom feed. */
final class FeedEntry
{
    public function __construct(
        public readonly string $guid,
        public readonly string $url,
        public readonly string $title,
        public readonly ?CarbonInterface $publishedAt,
        public readonly string $excerpt,
        public readonly ?string $content,
        public readonly ?string $imageUrl,
    ) {}

    /**
     * Drops tracking parameters, fragments and trailing slashes so the same
     * article seen through two feeds compares equal.
     */
    public static function normalizeUrl(string $url): string
    {
        $parts = parse_url(trim($url));
        if (! is_array($parts) || empty($parts['host']) || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return '';
        }

        $query = [];
        parse_str($parts['query'] ?? '', $query);
        foreach (array_keys($query) as $name) {
            if (str_starts_with(strtolower((string) $name), 'utm_') || in_array(strtolower((string) $name), ['fbclid', 'gclid', 'ref', 'source'], true)) {
                unset($query[$name]);
            }
        }

        $path = rtrim($parts['path'] ?? '', '/');

        return strtolower($parts['scheme']).'://'.strtolower($parts['host'])
            .(isset($parts['port']) ? ':'.$parts['port'] : '')
            .$path
            .($query ? '?'.http_build_query($query) : '');
    }
}
