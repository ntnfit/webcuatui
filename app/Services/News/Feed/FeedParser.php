<?php

namespace App\Services\News\Feed;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use SimpleXMLElement;
use Throwable;

/**
 * Parses RSS 2.0, RSS 1.0 (RDF) and Atom documents. Only the feed document itself is
 * read: no HTML page is ever fetched or scraped.
 */
class FeedParser
{
    private const NS_CONTENT = 'http://purl.org/rss/1.0/modules/content/';

    private const NS_MEDIA = 'http://search.yahoo.com/mrss/';

    private const NS_DC = 'http://purl.org/dc/elements/1.1/';

    private const EXCERPT_LIMIT = 1500;

    private const CONTENT_LIMIT = 6000;

    /** @return list<FeedEntry> */
    public function parse(string $xml): array
    {
        $xml = ltrim($xml, "\xEF\xBB\xBF \t\r\n");

        // Entities are the only way to smuggle in external files or expansion bombs: refuse them outright.
        if ($xml === '' || stripos($xml, '<!ENTITY') !== false) {
            throw new FeedException('Feed rỗng hoặc chứa khai báo ENTITY không an toàn.');
        }

        $previous = libxml_use_internal_errors(true);
        try {
            // LIBXML_NOENT is deliberately absent and LIBXML_NONET blocks any network access.
            $doc = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
            if ($doc === false) {
                throw new FeedException('XML không hợp lệ: '.trim((string) (libxml_get_errors()[0]->message ?? 'không đọc được')));
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $nodes = match ($doc->getName()) {
            'rss' => $doc->channel->item ?? [],
            'RDF' => $doc->item ?? [],
            'feed' => $doc->entry ?? [],
            default => throw new FeedException('Không phải RSS/Atom (root: '.$doc->getName().').'),
        };

        $entries = [];
        foreach ($nodes as $node) {
            $entry = $doc->getName() === 'feed' ? $this->atomEntry($node) : $this->rssEntry($node);
            if ($entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    private function rssEntry(SimpleXMLElement $item): ?FeedEntry
    {
        $url = FeedEntry::normalizeUrl((string) $item->link);
        $html = (string) $item->children(self::NS_CONTENT)->encoded;
        $description = (string) $item->description;
        $date = (string) $item->pubDate ?: (string) $item->children(self::NS_DC)->date;

        return $this->build(
            guid: (string) $item->guid,
            url: $url,
            title: (string) $item->title,
            date: $date,
            summaryHtml: $description !== '' ? $description : $html,
            contentHtml: $html,
            imageUrl: $this->rssImage($item) ?? $this->firstImgSrc($html ?: $description),
        );
    }

    private function atomEntry(SimpleXMLElement $entry): ?FeedEntry
    {
        $href = '';
        foreach ($entry->link as $link) {
            $rel = (string) ($link['rel'] ?? 'alternate');
            if ($rel === 'alternate' || $href === '') {
                $href = (string) $link['href'];
            }
        }
        $contentHtml = (string) $entry->content;

        return $this->build(
            guid: (string) $entry->id,
            url: FeedEntry::normalizeUrl($href),
            title: (string) $entry->title,
            date: (string) $entry->published ?: (string) $entry->updated,
            summaryHtml: (string) $entry->summary !== '' ? (string) $entry->summary : $contentHtml,
            contentHtml: $contentHtml,
            imageUrl: $this->rssImage($entry) ?? $this->firstImgSrc($contentHtml),
        );
    }

    private function build(string $guid, string $url, string $title, string $date, string $summaryHtml, string $contentHtml, ?string $imageUrl): ?FeedEntry
    {
        $title = $this->text($title);
        if ($url === '' || $title === '') {
            return null;
        }

        $content = $this->text($contentHtml);

        return new FeedEntry(
            guid: trim($guid) !== '' ? trim($guid) : $url,
            url: $url,
            title: mb_substr($title, 0, 500),
            publishedAt: $this->date($date),
            excerpt: mb_substr($this->text($summaryHtml), 0, self::EXCERPT_LIMIT),
            content: $content !== '' ? mb_substr($content, 0, self::CONTENT_LIMIT) : null,
            imageUrl: $imageUrl,
        );
    }

    private function rssImage(SimpleXMLElement $node): ?string
    {
        foreach ($node->enclosure ?? [] as $enclosure) {
            if (str_starts_with((string) $enclosure['type'], 'image/')) {
                return $this->httpUrl((string) $enclosure['url']);
            }
        }

        $media = $node->children(self::NS_MEDIA);
        foreach (['content', 'thumbnail'] as $tag) {
            foreach ($media->{$tag} ?? [] as $m) {
                $type = (string) $m['type'];
                if ($tag === 'thumbnail' || $type === '' || str_starts_with($type, 'image/') || (string) $m['medium'] === 'image') {
                    if ($url = $this->httpUrl((string) $m['url'])) {
                        return $url;
                    }
                }
            }
        }

        return null;
    }

    private function firstImgSrc(string $html): ?string
    {
        return preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m) ? $this->httpUrl(html_entity_decode($m[1])) : null;
    }

    private function httpUrl(string $url): ?string
    {
        $url = trim($url);

        return preg_match('#^https?://#i', $url) && mb_strlen($url) <= 1000 ? $url : null;
    }

    private function date(string $value): ?CarbonInterface
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    /** HTML to plain text: tags removed, entities decoded, whitespace collapsed. */
    private function text(string $html): string
    {
        $html = preg_replace('#<(script|style)\b.*?</\1>#is', ' ', $html) ?? $html;
        $html = preg_replace('#</(p|div|li|h[1-6]|br)>|<br\s*/?>#i', ' ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
