<?php

use App\Enums\PostStatus;
use App\Enums\TypePost;
use App\Models\blogs;
use App\Models\NewsItem;
use App\Models\NewsRun;
use App\Models\NewsSource;
use App\Services\News\NewsSettings;
use App\Services\News\Publishing\SitemapRefresher;
use App\Services\News\Writing\ArticleDraft;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Support\News\FakeArticleWriter;
use Tests\Support\News\NewsFixtures;

const NEWS_TITLES = [
    'Laravel queue improvements land', 'Shopify checkout API change announced', 'Magento security patch released',
    'SAP Business One update for partners', 'Claude model release for developers', 'Rust compiler performance news',
];

beforeEach(function () {
    Storage::fake('public');
    NewsFixtures::admin();
    config(['news.per_source_cap' => 10]);
    $this->sitemap = $this->mock(SitemapRefresher::class);
    $this->sitemap->shouldReceive('refresh')->byDefault();
});

/** Registers a source whose feed returns the given titles. */
function newsFeed(string $host, array $titles, array $sourceAttributes = []): NewsSource
{
    $items = array_map(fn ($t) => ['title' => $t, 'url' => "https://{$host}/".str($t)->slug()], $titles);
    Http::fake([$host.'/feed' => Http::response(NewsFixtures::rss($items))]);

    return NewsSource::factory()->create(['url' => "https://{$host}/feed", 'name' => $host] + $sourceAttributes);
}

it('fetches, writes and publishes posts with category, tags, SEO row, images and a code generated source block', function () {
    $this->sitemap->shouldReceive('refresh')->once();
    $source = newsFeed('news.example.test', ['Laravel queue improvements land']);
    NewsFixtures::bindWriter();

    $this->artisan('news:crawl')->assertSuccessful();

    $post = blogs::with(['categories', 'tags', 'seoDetail'])->firstOrFail();
    $item = NewsItem::firstOrFail();

    expect($post->status)->toBe(PostStatus::PUBLISHED)
        ->and($post->type)->toBe(TypePost::NEWS->value)
        ->and($post->published_at)->not->toBeNull()
        ->and($post->cover_photo_path)->toStartWith('news/')->toEndWith('.webp')
        ->and(Storage::disk('public')->exists($post->cover_photo_path))->toBeTrue()
        ->and($post->photo_alt_text)->not->toBe('')
        ->and($post->categories->pluck('name')->all())->toBe(['Tin công nghệ'])
        ->and($post->categories->first()->name_en)->toBe('Tech News')
        ->and($post->tags)->toHaveCount(2)
        ->and($post->seoDetail->title)->toContain('Tin công nghệ')
        ->and($post->seoDetail->keywords)->toHaveCount(5)
        ->and($post->body)->toContain('<h2>Nguồn tham khảo</h2>')
        ->toContain('href="https://news.example.test/laravel-queue-improvements-land" target="_blank" rel="nofollow noopener"')
        ->toContain('news.example.test')
        ->and($item->status)->toBe(NewsItem::STATUS_PUBLISHED)
        ->and($item->post_id)->toBe($post->id)
        ->and($source->fresh()->last_error)->toBeNull();

    $run = NewsRun::firstOrFail();
    expect([$run->status, $run->fetched, $run->selected, $run->published, $run->rejected, $run->failed])->toBe(['completed', 1, 1, 1, 0, 0]);
});

it('serves the published post with its source block on the public blog page and hides it once unpublished', function () {
    newsFeed('news.example.test', ['Laravel queue improvements land']);
    NewsFixtures::bindWriter();
    $this->withoutVite();
    $this->artisan('news:crawl')->assertSuccessful();
    $post = blogs::firstOrFail();

    $this->get('/blogs/'.$post->slug)->assertOk()->assertSee('Nguồn tham khảo', false)->assertSee('news.example.test/laravel-queue-improvements-land', false);

    $post->update(['status' => PostStatus::PENDING]);
    $this->get('/blogs/'.$post->slug)->assertNotFound();
});

it('never exceeds the daily cap across reruns and respects --limit', function () {
    config(['news.posts_per_day' => 3]);
    newsFeed('news.example.test', NEWS_TITLES);
    $writer = NewsFixtures::bindWriter();

    $this->artisan('news:crawl', ['--limit' => 1])->assertSuccessful();
    expect(blogs::count())->toBe(1);

    $this->artisan('news:crawl')->assertSuccessful();
    expect(blogs::count())->toBe(3);

    $callsBefore = count($writer->calls);
    $this->artisan('news:crawl')->assertSuccessful();

    expect(blogs::count())->toBe(3)
        ->and(count($writer->calls))->toBe($callsBefore)
        ->and(NewsRun::latest('id')->first()->notes)->toContain('Đã đủ số bài');
});

it('rejects items the model marks as not publishable and keeps going to fill the quota', function () {
    config(['news.posts_per_day' => 2]);
    newsFeed('news.example.test', array_slice(NEWS_TITLES, 0, 4));
    NewsFixtures::bindWriter(new FakeArticleWriter(function ($item) {
        return str_contains($item->title, 'Laravel') || str_contains($item->title, 'Shopify')
            ? ArticleDraft::rejected('Paywall, không đủ nội dung')
            : NewsFixtures::draft(['title' => 'Bài về '.$item->title, 'slug' => str($item->title)->slug()]);
    }));

    $this->artisan('news:crawl')->assertSuccessful();

    expect(blogs::count())->toBe(2)
        ->and(NewsItem::where('status', 'rejected')->count())->toBeGreaterThanOrEqual(1)
        ->and(NewsItem::where('status', 'rejected')->pluck('error')->unique()->all())->each->toContain('Paywall')
        ->and(NewsItem::where('status', 'published')->count())->toBe(2);
});

it('marks a rejected item without creating a post', function () {
    config(['news.posts_per_day' => 1, 'news.attempt_factor' => 1]);
    newsFeed('news.example.test', ['Claude model release for developers']);
    NewsFixtures::bindWriter(new FakeArticleWriter(fn () => ArticleDraft::rejected('Lạc chủ đề')));

    $this->artisan('news:crawl')->assertSuccessful();

    $item = NewsItem::firstOrFail();
    expect(blogs::count())->toBe(0)
        ->and($item->status)->toBe('rejected')
        ->and($item->error)->toBe('Lạc chủ đề')
        ->and(NewsRun::first()->rejected)->toBe(1);
});

it('records a failing item and still publishes the others', function () {
    config(['news.posts_per_day' => 2]);
    newsFeed('news.example.test', array_slice(NEWS_TITLES, 0, 3));
    $writer = NewsFixtures::bindWriter(new FakeArticleWriter(function ($item) {
        if (str_contains($item->title, 'Shopify')) {
            throw new RuntimeException('Claude API 529 overloaded');
        }

        return NewsFixtures::draft(['title' => 'Bài về '.$item->title, 'slug' => str($item->title)->slug()]);
    }));

    $this->artisan('news:crawl')->assertSuccessful();

    $failed = NewsItem::where('status', 'failed')->first();
    expect(blogs::count())->toBe(2)
        ->and($failed)->not->toBeNull()
        ->and($failed->error)->toContain('529')
        ->and(NewsRun::first()->failed)->toBe(1);
});

it('keeps the run alive when one source is down', function () {
    Http::fake([
        'down.test/feed' => Http::response('maintenance', 503),
        'up.test/feed' => Http::response(NewsFixtures::rss([['title' => 'Magento security patch released', 'url' => 'https://up.test/m']])),
    ]);
    $down = NewsSource::factory()->create(['url' => 'https://down.test/feed']);
    NewsSource::factory()->create(['url' => 'https://up.test/feed']);
    NewsFixtures::bindWriter();

    $this->artisan('news:crawl')->assertSuccessful();

    expect(blogs::count())->toBe(1)->and($down->fresh()->last_error)->toContain('503');
});

it('stops with a clear error and creates nothing when the API key is missing', function () {
    newsFeed('news.example.test', NEWS_TITLES);
    // Real writer, no key anywhere.
    config(['news.keys.anthropic' => null]);

    $this->artisan('news:crawl')->expectsOutputToContain('ANTHROPIC_API_KEY')->assertFailed();

    $run = NewsRun::firstOrFail();
    expect(blogs::count())->toBe(0)->and(NewsItem::count())->toBe(0)
        ->and($run->status)->toBe('aborted')->and($run->notes)->toContain('ANTHROPIC_API_KEY');
    Http::assertNothingSent();
});

it('dry run prints the plan and writes nothing at all', function () {
    $source = newsFeed('news.example.test', NEWS_TITLES);
    $writer = NewsFixtures::bindWriter();
    $this->sitemap->shouldNotReceive('refresh');

    $this->artisan('news:crawl', ['--dry-run' => true])
        ->expectsOutputToContain('Chạy thử')
        ->expectsOutputToContain('Laravel queue improvements land')
        ->assertSuccessful();

    expect(NewsItem::count())->toBe(0)->and(NewsRun::count())->toBe(0)->and(blogs::count())->toBe(0)
        ->and($writer->calls)->toBe([])
        ->and($source->fresh()->last_fetched_at)->toBeNull()
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

it('dry run works without an API key because it never calls Claude', function () {
    newsFeed('news.example.test', NEWS_TITLES);
    config(['news.keys.anthropic' => null]);

    $this->artisan('news:crawl', ['--dry-run' => true])->assertSuccessful();
});

it('saves drafts for review without touching the sitemap in draft mode', function () {
    app(NewsSettings::class)->set('mode', 'draft');
    $this->sitemap->shouldNotReceive('refresh');
    newsFeed('news.example.test', ['Claude model release for developers']);
    NewsFixtures::bindWriter();

    $this->artisan('news:crawl')->assertSuccessful();

    $post = blogs::firstOrFail();
    expect($post->status)->toBe(PostStatus::PENDING)->and($post->is_published)->toBeFalsy();
});

it('does nothing while the kill switch is off', function () {
    newsFeed('news.example.test', NEWS_TITLES);
    NewsFixtures::bindWriter();
    app(NewsSettings::class)->set('enabled', false);

    $this->artisan('news:crawl')->assertFailed();

    expect(blogs::count())->toBe(0)->and(NewsRun::count())->toBe(0);
});

it('registers an every minute scheduler entry that the kill switch disables', function () {
    Artisan::call('schedule:list');
    $event = collect(app(Schedule::class)->events())->first(fn ($e) => str_contains($e->command, 'news:crawl --scheduled'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('* * * * *')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue()
        ->and($event->filtersPass(app()))->toBeTrue();

    app(NewsSettings::class)->set('enabled', false);
    expect($event->filtersPass(app()))->toBeFalse();
});

it('limits a run to one source with --source', function () {
    config(['news.posts_per_day' => 5]);
    newsFeed('a.test', ['Laravel queue improvements land']);
    newsFeed('b.test', ['Magento security patch released']);
    NewsFixtures::bindWriter();

    $this->artisan('news:crawl', ['--source' => 'b.test'])->assertSuccessful();

    expect(NewsItem::pluck('title')->all())->toBe(['Magento security patch released']);
});
