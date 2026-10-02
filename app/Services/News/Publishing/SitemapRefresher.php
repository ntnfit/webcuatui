<?php

namespace App\Services\News\Publishing;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Makes new posts visible quickly: clears the cached home page list and rebuilds the sitemap file. */
class SitemapRefresher
{
    public function refresh(): void
    {
        Cache::forget('home.latest-articles');

        try {
            Artisan::call('app:generate-sitemap');
        } catch (Throwable $e) {
            // The dynamic /sitemap.xml route keeps working; the static file is a best-effort extra.
            Log::warning('Sitemap regeneration failed', ['error' => $e->getMessage()]);
        }
    }
}
