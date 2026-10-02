<?php

use App\Models\blogs;
use App\Models\NewsRun;
use App\Models\NewsSource;
use App\Services\News\NewsSettings;
use App\Services\News\Pipeline\ScheduleGate;
use App\Services\News\Publishing\SitemapRefresher;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Support\News\NewsFixtures;

/** Instants are given in Vietnam time (UTC+7) for readability. */
function newsVn(string $time): CarbonImmutable
{
    return CarbonImmutable::parse($time, 'Asia/Ho_Chi_Minh');
}

function newsRunAt(string $vnTime, string $status = 'completed'): NewsRun
{
    return NewsRun::factory()->create(['status' => $status, 'started_at' => newsVn($vnTime)->utc(), 'finished_at' => newsVn($vnTime)->utc()]);
}

it('does not run before the configured time', function () {
    expect(app(ScheduleGate::class)->shouldRun(newsVn('2026-10-05 08:59:00')))->toBeFalse();
});

it('runs at the configured time and catches up after a missed minute', function () {
    $gate = app(ScheduleGate::class);

    expect($gate->shouldRun(newsVn('2026-10-05 09:00:00')))->toBeTrue()
        ->and($gate->shouldRun(newsVn('2026-10-05 13:42:00')))->toBeTrue();
});

it('runs only once per day in the configured timezone', function () {
    $gate = app(ScheduleGate::class);
    newsRunAt('2026-10-05 09:01:00');

    expect($gate->shouldRun(newsVn('2026-10-05 09:02:00')))->toBeFalse()
        ->and($gate->shouldRun(newsVn('2026-10-05 23:59:00')))->toBeFalse()
        // Next calendar day in Vietnam, even though it is still the previous day in UTC.
        ->and($gate->shouldRun(newsVn('2026-10-06 09:00:00')))->toBeTrue();
});

it('counts the Vietnamese day, not the UTC day', function () {
    $gate = app(ScheduleGate::class);
    // 03:00 on the 5th in Vietnam is still the 4th in UTC.
    newsRunAt('2026-10-05 03:00:00');
    expect($gate->shouldRun(newsVn('2026-10-05 10:00:00')))->toBeFalse();

    NewsRun::query()->delete();
    newsRunAt('2026-10-04 23:30:00');
    expect($gate->shouldRun(newsVn('2026-10-05 10:00:00')))->toBeTrue();
});

it('retries an aborted run only after a pause and never overlaps a running one', function () {
    $gate = app(ScheduleGate::class);
    newsRunAt('2026-10-05 09:00:00', 'aborted');

    expect($gate->shouldRun(newsVn('2026-10-05 09:10:00')))->toBeFalse()
        ->and($gate->shouldRun(newsVn('2026-10-05 09:31:00')))->toBeTrue();

    newsRunAt('2026-10-05 09:30:00', 'running');
    expect($gate->shouldRun(newsVn('2026-10-05 09:40:00')))->toBeFalse();
});

it('honours the kill switch, a custom time and a custom timezone', function () {
    $settings = app(NewsSettings::class);
    $gate = app(ScheduleGate::class);

    $settings->set('run_time', '14:30');
    expect($gate->shouldRun(newsVn('2026-10-05 14:29:00')))->toBeFalse()->and($gate->shouldRun(newsVn('2026-10-05 14:30:00')))->toBeTrue();

    $settings->set('timezone', 'UTC');
    expect($gate->shouldRun(CarbonImmutable::parse('2026-10-05 14:00:00', 'UTC')))->toBeFalse()
        ->and($gate->shouldRun(CarbonImmutable::parse('2026-10-05 14:30:00', 'UTC')))->toBeTrue();

    $settings->set('enabled', false);
    expect($gate->shouldRun(CarbonImmutable::parse('2026-10-05 15:00:00', 'UTC')))->toBeFalse();
});

it('falls back to 09:00 when the stored time is malformed', function () {
    app(NewsSettings::class)->set('run_time', 'soon');

    expect(app(ScheduleGate::class)->shouldRun(newsVn('2026-10-05 08:00:00')))->toBeFalse()
        ->and(app(ScheduleGate::class)->shouldRun(newsVn('2026-10-05 09:00:00')))->toBeTrue();
});

it('the scheduled command publishes once a day and ignores ticks outside the window', function () {
    Storage::fake('public');
    NewsFixtures::admin();
    $this->mock(SitemapRefresher::class)->shouldReceive('refresh');
    Http::fake(['news.test/feed' => Http::response(NewsFixtures::rss([['title' => 'Claude model release for developers', 'url' => 'https://news.test/a', 'date' => newsVn('2026-10-05 08:00:00')->toRfc2822String()]]))]);
    NewsSource::factory()->create(['url' => 'https://news.test/feed']);
    NewsFixtures::bindWriter();

    $this->travelTo(newsVn('2026-10-05 08:30:00'));
    $this->artisan('news:crawl --scheduled')->assertSuccessful();
    expect(blogs::count())->toBe(0)->and(NewsRun::count())->toBe(0);

    $this->travelTo(newsVn('2026-10-05 09:03:00'));
    $this->artisan('news:crawl --scheduled')->assertSuccessful();
    expect(blogs::count())->toBe(1)->and(NewsRun::count())->toBe(1);

    $this->travelTo(newsVn('2026-10-05 09:04:00'));
    $this->artisan('news:crawl --scheduled')->assertSuccessful();
    $this->travelTo(newsVn('2026-10-05 18:00:00'));
    $this->artisan('news:crawl --scheduled')->assertSuccessful();
    expect(NewsRun::count())->toBe(1);
});
