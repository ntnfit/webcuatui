<?php

namespace App\Services\News\Selection;

use App\Models\blogs;
use App\Models\NewsItem;
use App\Services\News\Feed\FeedEntry;
use App\Services\News\NewsSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Picks which candidate items are worth sending to the writer: fresh enough, not a
 * duplicate of anything seen in the last days, scored, capped per source.
 */
class ItemSelector
{
    public function __construct(
        private readonly NewsSettings $settings,
        private readonly ItemScorer $scorer,
    ) {}

    /**
     * @param  Collection<int, NewsItem>  $candidates  items with status "new" and their source loaded
     * @param  int  $limit  how many ranked candidates to return
     * @return Collection<int, NewsItem> ranked best first, score assigned
     */
    public function select(Collection $candidates, int $limit, ?CarbonImmutable $now = null): Collection
    {
        $now ??= now()->toImmutable();
        $maxAge = (int) $this->settings->get('max_age_hours');
        $cap = max(1, (int) $this->settings->get('per_source_cap'));
        $threshold = (float) $this->settings->get('title_similarity');
        $blocked = $this->lowerList($this->settings->get('blocked_keywords', []));
        $boost = $this->lowerList($this->settings->get('boost_keywords', []));

        [$knownUrls, $knownTitles] = $this->history($candidates, $now);

        $ranked = $candidates
            ->filter(fn (NewsItem $i) => $i->published_at === null || $i->published_at->greaterThanOrEqualTo($now->subHours($maxAge)))
            ->reject(fn (NewsItem $i) => $this->isBlocked($i, $blocked))
            ->reject(fn (NewsItem $i) => isset($knownUrls[FeedEntry::normalizeUrl($i->url)]))
            ->each(fn (NewsItem $i) => $i->score = $this->scorer->score($i, $now, $maxAge, $boost))
            ->sortByDesc(fn (NewsItem $i) => [$i->score, $i->published_at?->getTimestamp() ?? 0])
            ->values();

        $picked = collect();
        $perSource = [];
        $pickedTitles = [];

        foreach ($ranked as $item) {
            if ($picked->count() >= $limit) {
                break;
            }
            if (($perSource[$item->source_id] ?? 0) >= $cap) {
                continue;
            }
            $normalized = TitleSimilarity::normalize($item->title);
            if (TitleSimilarity::matchesAny($normalized, $knownTitles, $threshold)
                || TitleSimilarity::matchesAny($normalized, $pickedTitles, $threshold)) {
                continue;
            }

            $picked->push($item);
            $pickedTitles[] = $normalized;
            $perSource[$item->source_id] = ($perSource[$item->source_id] ?? 0) + 1;
        }

        return $picked;
    }

    /**
     * URLs and normalised titles already handled in the last days, from news items and posts.
     *
     * @return array{0: array<string, true>, 1: list<string>}
     */
    private function history(Collection $candidates, CarbonImmutable $now): array
    {
        $since = $now->subDays((int) $this->settings->get('dedupe_days'));
        $candidateIds = $candidates->pluck('id')->filter()->all();

        $items = NewsItem::query()
            ->where('created_at', '>=', $since)
            ->where('status', '!=', NewsItem::STATUS_NEW)
            ->whereNotIn('id', $candidateIds)
            ->get(['url', 'title']);

        $urls = [];
        $titles = [];
        foreach ($items as $item) {
            $urls[FeedEntry::normalizeUrl($item->url)] = true;
            $titles[] = TitleSimilarity::normalize($item->title);
        }

        // Titles of every recent post, whoever wrote it, so the bot never repeats a story.
        foreach (blogs::query()->where('created_at', '>=', $since)->pluck('title') as $title) {
            $titles[] = TitleSimilarity::normalize($title);
        }

        return [$urls, array_values(array_filter(array_unique($titles)))];
    }

    private function isBlocked(NewsItem $item, array $blocked): bool
    {
        if ($blocked === []) {
            return false;
        }
        $haystack = mb_strtolower($item->title.' '.$item->excerpt.' '.parse_url($item->url, PHP_URL_HOST));

        foreach ($blocked as $word) {
            if (str_contains($haystack, $word)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    private function lowerList(mixed $list): array
    {
        return array_values(array_filter(array_map(fn ($v) => mb_strtolower(trim((string) $v)), (array) $list)));
    }
}
