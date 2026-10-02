<?php

use App\Models\NewsSource;
use App\Services\News\Feed\FeedException;
use App\Services\News\Feed\FeedFetcher;
use App\Services\News\Feed\FeedParser;
use Illuminate\Support\Facades\Http;
use Tests\Support\News\NewsFixtures;

it('parses an RSS feed with content:encoded, media image and tracking parameters stripped', function () {
    $xml = NewsFixtures::rss([[
        'title' => 'Claude gets <b>faster</b>',
        'url' => 'https://example.test/post/?utm_source=rss&id=7#top',
        'content' => '<p>Full <em>text</em> from the feed.</p>',
        'image' => 'https://example.test/cover.jpg',
    ]]);

    $entries = (new FeedParser)->parse($xml);

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->title)->toBe('Claude gets faster')
        ->and($entries[0]->url)->toBe('https://example.test/post?id=7')
        ->and($entries[0]->content)->toBe('Full text from the feed.')
        ->and($entries[0]->imageUrl)->toBe('https://example.test/cover.jpg')
        ->and($entries[0]->publishedAt)->not->toBeNull();
});

it('parses an Atom feed and prefers the alternate link', function () {
    $atom = '<?xml version="1.0"?><feed xmlns="http://www.w3.org/2005/Atom"><title>x</title><entry>'
        .'<title>Atom entry</title><id>tag:example.test,2026:1</id>'
        .'<link rel="self" href="https://example.test/self"/><link rel="alternate" href="https://example.test/atom-entry"/>'
        .'<published>2026-10-01T08:00:00Z</published><summary>Atom summary</summary></entry></feed>';

    $entries = (new FeedParser)->parse($atom);

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->url)->toBe('https://example.test/atom-entry')
        ->and($entries[0]->guid)->toBe('tag:example.test,2026:1')
        ->and($entries[0]->excerpt)->toBe('Atom summary');
});

it('rejects malformed XML, non feed documents and entity declarations', function (string $body) {
    expect(fn () => (new FeedParser)->parse($body))->toThrow(FeedException::class);
})->with([
    'malformed' => '<rss><channel><item><title>broken',
    'html page' => '<html><body>not a feed</body></html>',
    'xxe' => '<?xml version="1.0"?><!DOCTYPE r [<!ENTITY x SYSTEM "file:///etc/passwd">]><rss><channel><item><title>&x;</title><link>https://a.test/1</link></item></channel></rss>',
    'empty' => '',
]);

it('skips entries without a usable link or title', function () {
    $xml = NewsFixtures::rss([
        ['title' => 'No link', 'url' => 'javascript:alert(1)'],
        ['title' => 'Fine', 'url' => 'https://example.test/fine'],
    ]);

    expect((new FeedParser)->parse($xml))->toHaveCount(1);
});

it('records a failing source without throwing and keeps the next source working', function () {
    Http::fake([
        'https://bad.test/feed' => Http::response('boom', 500),
        'https://good.test/feed' => Http::response(NewsFixtures::rss([['title' => 'Ok', 'url' => 'https://good.test/1']])),
    ]);
    $bad = NewsSource::factory()->create(['url' => 'https://bad.test/feed']);
    $good = NewsSource::factory()->create(['url' => 'https://good.test/feed']);
    $fetcher = app(FeedFetcher::class);

    expect($fetcher->collect($bad))->toBe([])
        ->and($fetcher->collect($good))->toHaveCount(1)
        ->and($bad->fresh()->last_error)->toContain('500')
        ->and($good->fresh()->last_error)->toBeNull()
        ->and($good->fresh()->last_fetched_at)->not->toBeNull();

    Http::assertSent(fn ($request) => str_contains($request->header('User-Agent')[0] ?? '', 'ToilamERPNewsBot'));
});

it('refuses non http(s) feed URLs', function () {
    $source = NewsSource::factory()->create(['url' => 'file:///etc/passwd']);

    expect(app(FeedFetcher::class)->collect($source))->toBe([])
        ->and($source->fresh()->last_error)->toContain('http');
});
