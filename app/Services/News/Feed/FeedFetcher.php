<?php

namespace App\Services\News\Feed;

use App\Models\NewsSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Downloads one source feed. A failing source is recorded and never aborts the run. */
class FeedFetcher
{
    public function __construct(private readonly FeedParser $parser) {}

    /**
     * @return list<FeedEntry> empty when the source failed (the error is stored on the source)
     */
    public function collect(NewsSource $source, bool $record = true): array
    {
        try {
            $entries = $this->fetch($source);
            if ($record) {
                $source->forceFill(['last_fetched_at' => now(), 'last_error' => null])->save();
            }

            return $entries;
        } catch (Throwable $e) {
            Log::warning('News feed failed', ['source' => $source->name, 'error' => $e->getMessage()]);
            if ($record) {
                $source->forceFill(['last_fetched_at' => now(), 'last_error' => mb_substr($e->getMessage(), 0, 1000)])->save();
            }

            return [];
        }
    }

    /**
     * @return list<FeedEntry>
     *
     * @throws FeedException
     */
    public function fetch(NewsSource $source): array
    {
        if (! preg_match('#^https?://#i', $source->url)) {
            throw new FeedException('Chỉ hỗ trợ URL http(s).');
        }

        try {
            $response = Http::withHeaders(['User-Agent' => config('news.http.user_agent')])
                ->accept('application/rss+xml, application/atom+xml, application/xml;q=0.9, text/xml;q=0.8, */*;q=0.5')
                ->timeout((int) config('news.http.timeout'))
                ->connectTimeout(10)
                ->get($source->url);
        } catch (Throwable $e) {
            throw new FeedException('Không tải được feed: '.$e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            throw new FeedException('HTTP '.$response->status());
        }

        $body = $response->body();
        if (strlen($body) > (int) config('news.http.max_feed_bytes')) {
            throw new FeedException('Feed quá lớn.');
        }

        return $this->parser->parse($body);
    }
}
