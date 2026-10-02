<?php

namespace App\Console\Commands;

use App\Services\News\Pipeline\NewsPipeline;
use App\Services\News\Pipeline\RunOptions;
use App\Services\News\Pipeline\ScheduleGate;
use Illuminate\Console\Command;

class CrawlNewsCommand extends Command
{
    protected $signature = 'news:crawl
        {--dry-run : Fetch and select only; print what would be published without calling Claude or writing anything}
        {--limit= : Publish at most this many posts in this run (never above the daily cap)}
        {--source= : Only read one source (id or part of its name)}
        {--scheduled : Called by the every-minute scheduler: run only once per day at the configured time}';

    protected $description = 'Collect tech news, rewrite it as Vietnamese SEO articles with Claude and publish them';

    public function handle(NewsPipeline $pipeline, ScheduleGate $gate): int
    {
        if ($this->option('scheduled') && ! $gate->shouldRun()) {
            return self::SUCCESS;
        }

        set_time_limit(0);

        $limit = $this->option('limit');
        if ($limit !== null && (! ctype_digit((string) $limit))) {
            $this->error('--limit phải là số nguyên không âm.');

            return self::INVALID;
        }

        $dry = (bool) $this->option('dry-run');
        $result = $pipeline->run(new RunOptions($dry, $limit !== null ? (int) $limit : null, $this->option('source')));

        if ($result->failed()) {
            $this->error($result->error);

            return self::FAILURE;
        }

        if ($dry) {
            $this->info("Chạy thử: sẽ đăng {$result->preview->count()} bài (còn {$result->remainingQuota} suất hôm nay).");
            $this->table(
                ['#', 'Điểm', 'Nguồn', 'Tiêu đề', 'URL'],
                $result->preview->values()->map(fn ($item, $i) => [$i + 1, $item->score, $item->source?->name, mb_substr($item->title, 0, 70), $item->url])->all(),
            );

            return self::SUCCESS;
        }

        $run = $result->run;
        $this->info(sprintf(
            'Xong: lấy %d mục, chọn %d, đăng %d, loại %d, lỗi %d.',
            $run->fetched, $run->selected, $run->published, $run->rejected, $run->failed,
        ));

        return self::SUCCESS;
    }
}
