<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Spatie\Sitemap\SitemapGenerator;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:generate-sitemap', function () {
    SitemapGenerator::create('https://toilamerp.com')->writeToFile(public_path('sitemap.xml'));
})->purpose('Generate sitemap')->daily();
