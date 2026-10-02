<?php

use App\Services\News\NewsSettings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Sitemap\SitemapGenerator;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:generate-sitemap', function () {
    SitemapGenerator::create('https://toilamerp.com')->writeToFile(public_path('sitemap.xml'));
})->purpose('Generate sitemap')->daily();

// Daily news auto-poster. The run time (default 09:00 Asia/Ho_Chi_Minh) is editable in the admin
// settings page, so the scheduler ticks every minute and the command itself runs once per day at
// or after that time. The `when` filter is the kill switch (setting "enabled" / NEWS_AUTOPUBLISH).
Schedule::command('news:crawl --scheduled')
    ->everyMinute()
    ->withoutOverlapping(180)
    ->onOneServer()
    ->when(fn () => app(NewsSettings::class)->enabled());
