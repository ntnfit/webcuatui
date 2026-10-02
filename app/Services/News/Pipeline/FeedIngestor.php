<?php

namespace App\Services\News\Pipeline;

use App\Models\NewsItem;
use App\Models\NewsSource;
use App\Services\News\Feed\FeedEntry;
use App\Services\News\Feed\FeedFetcher;
use App\Services\News\NewsSettings;
use Illuminate\Support\Collection;

/**
 * Reads every source and turns fresh entries into news items. Entries already known
 * (same guid or same URL) are not stored twice. A dry run builds unsaved items only.
 */
class FeedIngestor
{
    public function __construct(
        private readonly FeedFetcher $fetcher,
        private readonly NewsSettings $settings,
    ) {}

    /**
     * @param  Collection<int, NewsSource>  $sources
     * @return array{candidates: Collection<int, NewsItem>, fetched: int}
     */
    public function gather(Collection $sources, bool $dryRun): array
    {
        $cutoff = now()->subHours((int) $this->settings->get('max_age_hours'));
        $perSource = max(1, (int) $this->settings->get('ingest_per_source'));
        $fetched = 0;
        $fresh = collect();

        foreach ($sources as $source) {
            $entries = $this->fetcher->collect($source, record: ! $dryRun);
            $fetched += count($entries);

            foreach (array_slice($entries, 0, $perSource) as $entry) {
                if ($entry->publishedAt && $entry->publishedAt->lessThan($cutoff)) {
                    continue;
                }
                if ($item = $this->ingest($source, $entry, $dryRun)) {
                    $fresh->push($item);
                }
            }
        }

        // Items stored by an earlier run that were never picked stay eligible while they are fresh.
        $stored = NewsItem::with('source')
            ->where('status', NewsItem::STATUS_NEW)
            ->whereIn('source_id', $sources->pluck('id'))
            ->where('published_at', '>=', $cutoff)
            ->get();

        $candidates = $stored->concat($fresh)->unique(fn (NewsItem $i) => $i->guid_hash)->values();

        return ['candidates' => $candidates, 'fetched' => $fetched];
    }

    private function ingest(NewsSource $source, FeedEntry $entry, bool $dryRun): ?NewsItem
    {
        $hash = NewsItem::hashFor($entry->guid);
        if (NewsItem::where('guid_hash', $hash)->orWhere('url', $entry->url)->exists()) {
            return null;
        }

        $attributes = [
            'source_id' => $source->id,
            'guid_hash' => $hash,
            'url' => $entry->url,
            'title' => $entry->title,
            // Feeds without a date are treated as fresh at fetch time.
            'published_at' => $entry->publishedAt ?? now(),
            'excerpt' => $entry->excerpt,
            'content' => $entry->content,
            'image_url' => $entry->imageUrl,
            'status' => NewsItem::STATUS_NEW,
        ];

        $item = $dryRun ? new NewsItem($attributes) : NewsItem::create($attributes);
        $item->setRelation('source', $source);

        return $item;
    }
}
