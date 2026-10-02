<?php

namespace App\Providers;

use App\Services\News\Content\HtmlSanitizer;
use App\Services\News\NewsSettings;
use App\Services\News\Writing\ArticleWriter;
use App\Services\News\Writing\ClaudeArticleWriter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NewsSettings::class);
        $this->app->bind(ArticleWriter::class, ClaudeArticleWriter::class);

        // Links to the site itself are internal; every other absolute link gets nofollow + new tab.
        $this->app->bind(HtmlSanitizer::class, fn () => new HtmlSanitizer(array_filter([
            parse_url((string) config('app.url'), PHP_URL_HOST),
            'toilamerp.com',
            'www.toilamerp.com',
        ])));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
