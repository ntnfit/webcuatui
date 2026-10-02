<?php

namespace App\Jobs;

use App\Services\News\Pipeline\NewsPipeline;
use App\Services\News\Pipeline\RunOptions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/** Queued entry point of the news auto-poster (admin "Chạy ngay"). Same pipeline as `news:crawl`. */
class RunNewsCrawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** A full run writes many articles one by one. */
    public int $timeout = 3600;

    public int $tries = 1;

    public function handle(NewsPipeline $pipeline): void
    {
        $result = $pipeline->run(new RunOptions);

        if ($result->failed()) {
            Log::error('Queued news run failed: '.$result->error);
        }
    }
}
