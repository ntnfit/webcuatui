<?php

namespace App\Services\News\Pipeline;

use App\Models\NewsItem;
use App\Models\NewsRun;
use App\Models\NewsSource;
use App\Services\News\Images\ImageService;
use App\Services\News\Images\ImageSet;
use App\Services\News\NewsSettings;
use App\Services\News\Publishing\PostPublisher;
use App\Services\News\Publishing\SitemapRefresher;
use App\Services\News\Selection\ItemSelector;
use App\Services\News\Writing\ArticleRejectedException;
use App\Services\News\Writing\ArticleValidator;
use App\Services\News\Writing\ArticleWriter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * One execution of the news auto-poster: fetch, select, write, publish.
 * Each item is isolated: a failing source or article is recorded and the run goes on.
 */
class NewsPipeline
{
    public function __construct(
        private readonly NewsSettings $settings,
        private readonly DailyQuota $quota,
        private readonly FeedIngestor $ingestor,
        private readonly ItemSelector $selector,
        private readonly ArticleWriter $writer,
        private readonly ArticleValidator $validator,
        private readonly ImageService $images,
        private readonly PostPublisher $publisher,
        private readonly SitemapRefresher $sitemap,
    ) {}

    public function run(RunOptions $options): RunResult
    {
        if (! $options->dryRun && ! $this->settings->enabled()) {
            return RunResult::aborted(null, 'Tính năng tin tự động đang tắt.');
        }

        $run = null;
        if (! $options->dryRun) {
            $run = NewsRun::create([
                'status' => NewsRun::STATUS_RUNNING, 'started_at' => now(),
                'fetched' => 0, 'selected' => 0, 'published' => 0, 'rejected' => 0, 'failed' => 0,
            ]);

            if (! $this->writer->isConfigured()) {
                $message = 'Thiếu ANTHROPIC_API_KEY (hoặc chưa lưu key trong trang cấu hình): không tạo bài nào.';
                Log::error('News run aborted: '.$message);

                return $this->abort($run, $message);
            }
        }

        try {
            return $this->execute($options, $run);
        } catch (Throwable $e) {
            Log::error('News run crashed', ['error' => $e->getMessage()]);
            report($e);

            return $run ? $this->abort($run, 'Lỗi không mong muốn: '.$e->getMessage()) : RunResult::aborted(null, $e->getMessage());
        }
    }

    private function execute(RunOptions $options, ?NewsRun $run): RunResult
    {
        $remaining = $this->quota->remaining();
        if ($options->limit !== null) {
            $remaining = min($remaining, max(0, $options->limit));
        }
        if ($remaining === 0) {
            $run && $this->finish($run, 'Đã đủ số bài trong ngày, không chạy thêm.');

            return new RunResult($run, collect(), null, 0);
        }

        $sources = $this->sources($options->source);
        ['candidates' => $candidates, 'fetched' => $fetched] = $this->ingestor->gather($sources, $options->dryRun);
        $run?->fill(['fetched' => $fetched]);

        $attempts = $remaining * max(1, (int) $this->settings->get('attempt_factor'));
        $ranked = $this->selector->select($candidates, $attempts);

        if ($options->dryRun) {
            return new RunResult(null, $ranked->take($remaining)->values(), null, $remaining);
        }

        $published = 0;
        foreach ($ranked as $item) {
            if ($published >= $remaining) {
                break;
            }
            $run->selected++;
            $item->forceFill(['status' => NewsItem::STATUS_SELECTED])->save();
            $this->process($item, $run) && $published++;
            $run->save(); // progress stays visible in the admin log while the run is going
        }

        $this->finish($run, sprintf('%d nguồn, %d mục, đăng %d/%d bài.', $sources->count(), $fetched, $published, $remaining));
        if ($run->published > 0 && $this->settings->get('mode') !== 'draft') {
            $this->sitemap->refresh();
        }

        return new RunResult($run, collect(), null, $remaining - $published);
    }

    /** @return bool true when a post was created */
    private function process(NewsItem $item, NewsRun $run): bool
    {
        /** @var ImageSet|null $images */
        $images = null;

        try {
            $clean = $this->validator->validate($this->writer->write($item), $item);
            $category = (string) ($item->source?->category?->name ?? $this->settings->get('default_category'));
            $images = $this->images->build($clean, $item, $category);
            $this->publisher->publish($item, $clean, $images);
            $run->published++;

            return true;
        } catch (ArticleRejectedException $e) {
            $item->forceFill(['status' => NewsItem::STATUS_REJECTED, 'error' => mb_substr($e->getMessage(), 0, 1000)])->save();
            $run->rejected++;
        } catch (Throwable $e) {
            Log::warning('News item failed', ['item' => $item->id, 'error' => $e->getMessage()]);
            $this->discardImages($images);
            $item->forceFill(['status' => NewsItem::STATUS_FAILED, 'error' => mb_substr($e->getMessage(), 0, 1000)])->save();
            $run->failed++;
        }

        return false;
    }

    /** Files saved for an article that could not be published. */
    private function discardImages(?ImageSet $images): void
    {
        foreach ($images ? [$images->cover, ...$images->inline] : [] as $image) {
            Storage::disk('public')->delete($image->path);
        }
    }

    /** @return Collection<int, NewsSource> */
    private function sources(?string $filter): Collection
    {
        $query = NewsSource::query()->where('enabled', true);

        if ($filter !== null && $filter !== '') {
            $query->where(fn ($q) => ctype_digit($filter)
                ? $q->whereKey((int) $filter)
                : $q->where('name', 'like', '%'.$filter.'%'));
        }

        return $query->with('category')->get();
    }

    private function finish(NewsRun $run, string $note): void
    {
        $run->addNote($note);
        $run->forceFill(['status' => NewsRun::STATUS_COMPLETED, 'finished_at' => now()])->save();
    }

    private function abort(NewsRun $run, string $message): RunResult
    {
        $run->addNote($message);
        $run->forceFill(['status' => NewsRun::STATUS_ABORTED, 'finished_at' => now()])->save();

        return RunResult::aborted($run, $message);
    }
}
