<?php

use App\Models\NewsItem;
use App\Models\NewsSource;
use App\Services\News\Images\CoverGenerator;
use App\Services\News\Images\ImageProcessor;
use App\Services\News\Images\ImageService;
use App\Services\News\NewsSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Support\News\NewsFixtures;

beforeEach(function () {
    Storage::fake('public');
});

function newsPng(): string
{
    $im = imagecreatetruecolor(1600, 900);
    imagefilledrectangle($im, 0, 0, 1600, 900, imagecolorallocate($im, 30, 120, 200));
    ob_start();
    imagepng($im);

    return (string) ob_get_clean();
}

function newsUnsplashFake(): void
{
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(['results' => array_map(fn ($n) => [
            'urls' => ['regular' => "https://images.unsplash.test/photo-{$n}.jpg"],
            'links' => ['html' => "https://unsplash.com/photos/{$n}", 'download_location' => "https://api.unsplash.com/photos/{$n}/download"],
            'user' => ['name' => "Ảnh Gia {$n}", 'links' => ['html' => "https://unsplash.com/@gia{$n}"]],
        ], [1, 2, 3])]),
        'images.unsplash.test/*' => Http::response(newsPng(), 200, ['Content-Type' => 'image/png']),
        'api.unsplash.com/photos/*' => Http::response(['url' => 'x']),
    ]);
}

function newsImageItem(array $attributes = []): NewsItem
{
    return NewsItem::factory()->for(NewsSource::factory()->create(['name' => 'Example']), 'source')->create($attributes);
}

it('renders a WebP cover for a Vietnamese title with the bundled font', function () {
    $png = app(CoverGenerator::class)->render('Hóa đơn điện tử: cập nhật quy định mới cho doanh nghiệp Việt Nam', 'Tin công nghệ', 'toilamerp.com');
    $path = app(ImageProcessor::class)->store($png, 'hoa-don-dien-tu');

    $info = getimagesizefromstring(Storage::disk('public')->get($path));

    expect(is_file(resource_path('fonts/BeVietnamPro-Bold.ttf')))->toBeTrue()
        ->and($path)->toEndWith('.webp')->toStartWith('news/')
        ->and($info['mime'])->toBe('image/webp')
        ->and([$info[0], $info[1]])->toBe([1200, 630])
        ->and(Storage::disk('public')->size($path))->toBeGreaterThan(2000);
});

it('refuses data that is not an image', function () {
    expect(fn () => app(ImageProcessor::class)->store('<?php echo 1;', 'x'))->toThrow(RuntimeException::class);
});

it('ships a generated cover when no stock key is configured, without any HTTP call', function () {
    Http::fake();
    $draft = NewsFixtures::draft();

    $set = app(ImageService::class)->build($draft, newsImageItem(), 'Tin công nghệ');

    Http::assertNothingSent();
    expect(Storage::disk('public')->exists($set->cover->path))->toBeTrue()
        ->and($set->cover->creditHtml)->toBeNull()
        ->and($set->inline)->toBe([]);
});

it('uses Unsplash for the cover and inline images, credits the photographer and pings the download endpoint', function () {
    newsUnsplashFake();
    app(NewsSettings::class)->setSecret('unsplash', 'unsplash-test-key');

    $set = app(ImageService::class)->build(NewsFixtures::draft(), newsImageItem(), 'Tin công nghệ');

    expect($set->inline)->toHaveCount(2)
        ->and($set->cover->creditHtml)->toContain('Photo by')->toContain('Ảnh Gia 1')->toContain('on')->toContain('Unsplash')
        ->toContain('utm_source=toilamerp')->toContain('rel="nofollow noopener"')
        ->and(Storage::disk('public')->exists($set->cover->path))->toBeTrue();
    Http::assertSent(fn ($r) => str_contains($r->url(), 'api.unsplash.com/photos/1/download') && $r->header('Authorization')[0] === 'Client-ID unsplash-test-key');
    Http::assertSent(fn ($r) => str_contains($r->url(), 'api.unsplash.com/search/photos') && $r['query'] === 'artificial intelligence');
});

it('falls back to Pexels when only a Pexels key exists and credits the photographer', function () {
    Http::fake([
        'api.pexels.com/*' => Http::response(['photos' => [['src' => ['large2x' => 'https://images.pexels.test/p.jpg'], 'photographer' => 'Lan Pham', 'photographer_url' => 'https://www.pexels.com/@lan', 'url' => 'https://www.pexels.com/photo/1']]]),
        'images.pexels.test/*' => Http::response(newsPng()),
    ]);
    app(NewsSettings::class)->setSecret('pexels', 'pexels-test-key');

    $set = app(ImageService::class)->build(NewsFixtures::draft(), newsImageItem(), 'Tin công nghệ');

    expect($set->cover->creditHtml)->toContain('Photo by')->toContain('Lan Pham')->toContain('Pexels');
});

it('still publishes with a generated cover when the stock API fails', function () {
    Http::fake(['api.unsplash.com/*' => Http::response('nope', 500)]);
    app(NewsSettings::class)->setSecret('unsplash', 'k');

    $set = app(ImageService::class)->build(NewsFixtures::draft(), newsImageItem(), 'Tin công nghệ');

    expect($set->cover->creditHtml)->toBeNull()->and(Storage::disk('public')->exists($set->cover->path))->toBeTrue();
});

it('never calls a stock API in generated only mode', function () {
    Http::fake();
    app(NewsSettings::class)->setSecret('unsplash', 'k');
    app(NewsSettings::class)->set('images.mode', 'generated_only');

    app(ImageService::class)->build(NewsFixtures::draft(), newsImageItem(), 'Tin công nghệ');

    Http::assertNothingSent();
});

it('does not touch the source article image unless explicitly enabled', function () {
    Http::fake(['source.test/*' => Http::response(newsPng())]);
    $item = newsImageItem(['image_url' => 'https://source.test/og.jpg']);

    app(ImageService::class)->build(NewsFixtures::draft(), $item, 'Tin công nghệ');
    Http::assertNothingSent();

    config(['news.images.use_source_image' => true]);
    $set = app(ImageService::class)->build(NewsFixtures::draft(), $item, 'Tin công nghệ');

    Http::assertSent(fn ($r) => $r->url() === 'https://source.test/og.jpg');
    expect($set->cover->creditHtml)->toContain('Example');
});
