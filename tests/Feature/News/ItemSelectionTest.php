<?php

use App\Enums\PostStatus;
use App\Enums\TypePost;
use App\Models\blogs;
use App\Models\NewsItem;
use App\Models\NewsSource;
use App\Models\User;
use App\Services\News\Selection\ItemSelector;

function newsCandidate(NewsSource $source, string $title, array $attributes = []): NewsItem
{
    return NewsItem::factory()->for($source, 'source')->create($attributes + ['title' => $title, 'excerpt' => 'plain excerpt']);
}

function newsSelect(array $items, int $limit = 10)
{
    return app(ItemSelector::class)->select(collect($items)->each->load('source'), $limit);
}

it('drops items older than the maximum age', function () {
    $source = NewsSource::factory()->create();
    $fresh = newsCandidate($source, 'Fresh AI release', ['published_at' => now()->subHours(3)]);
    newsCandidate($source, 'Stale story about databases', ['published_at' => now()->subHours(40)]);

    expect(newsSelect(NewsItem::all()->all())->pluck('id')->all())->toBe([$fresh->id]);
});

it('drops near duplicate titles within the batch and against recent history', function () {
    $source = NewsSource::factory()->create();
    $other = NewsSource::factory()->create();
    newsCandidate($other, 'OpenAI launches new model for developers', ['status' => NewsItem::STATUS_PUBLISHED, 'created_at' => now()->subDays(2)]);
    $dupOfHistory = newsCandidate($source, 'OpenAI launches a new model for developers');
    $a = newsCandidate($source, 'Shopify adds new checkout API');
    $b = newsCandidate($other, 'Shopify adds new checkout APIs');
    $unique = newsCandidate($other, 'Magento security patch released');

    $ids = newsSelect([$dupOfHistory, $a, $b, $unique])->pluck('id')->all();

    expect($ids)->not->toContain($dupOfHistory->id)
        ->and(count(array_intersect($ids, [$a->id, $b->id])))->toBe(1)
        ->and($ids)->toContain($unique->id);
});

it('drops titles that match an existing post and urls already published', function () {
    $source = NewsSource::factory()->create();
    blogs::create([
        'title' => 'Magento security patch released', 'slug' => 'm', 'body' => 'x', 'status' => PostStatus::PUBLISHED,
        'cover_photo_path' => 'a.webp', 'photo_alt_text' => 'a', 'user_id' => User::factory()->create()->id, 'type' => TypePost::NEWS->value,
    ]);
    $samePostTitle = newsCandidate($source, 'Magento security patch released');
    newsCandidate($source, 'Old published', ['status' => NewsItem::STATUS_PUBLISHED, 'url' => 'https://news.example.test/dup?utm_source=x']);
    $sameUrl = newsCandidate($source, 'Totally different words here', ['url' => 'https://news.example.test/dup']);
    $ok = newsCandidate($source, 'Laravel 13 ships queue improvements');

    expect(newsSelect([$samePostTitle, $sameUrl, $ok])->pluck('id')->all())->toBe([$ok->id]);
});

it('caps items per source and ranks by relevance, freshness and weight', function () {
    config(['news.per_source_cap' => 2]);
    $busy = NewsSource::factory()->create(['weight' => 1]);
    $heavy = NewsSource::factory()->create(['weight' => 9]);
    $items = [];
    foreach (['AI agents for ERP automation', 'LLM security tips', 'Programming news roundup', 'Cloud pricing update'] as $title) {
        $items[] = newsCandidate($busy, $title);
    }
    $top = newsCandidate($heavy, 'Cooking recipes weekly');

    $picked = newsSelect([...$items, $top], 10);

    expect($picked->where('source_id', $busy->id))->toHaveCount(2)
        ->and($picked->first()->id)->toBe($top->id)
        ->and($picked->pluck('score')->all())->toBe($picked->pluck('score')->sortDesc()->values()->all());
});

it('honours blocked keywords, boost keywords and the limit', function () {
    $source = NewsSource::factory()->create();
    $blocked = newsCandidate($source, 'Crypto casino bonus news');
    $boosted = newsCandidate($source, 'Odoo module guide');
    $plain = newsCandidate($source, 'Gardening notes');
    config(['news.blocked_keywords' => ['casino'], 'news.boost_keywords' => ['odoo']]);

    $picked = newsSelect([$blocked, $boosted, $plain], 1);

    expect($picked->pluck('id')->all())->toBe([$boosted->id]);
});
