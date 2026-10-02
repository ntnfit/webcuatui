<?php

namespace App\Services\News\Selection;

use App\Models\NewsItem;
use Carbon\CarbonInterface;

/** Cheap heuristic score: freshness + source weight + keyword relevance. No LLM involved. */
class ItemScorer
{
    /** Topics the site covers; every hit in the title or excerpt raises the score. */
    public const KEYWORDS = [
        'ai', 'llm', 'gpt', 'claude', 'openai', 'anthropic', 'machine learning', 'agent', 'lập trình', 'programming',
        'developer', 'laravel', 'php', 'python', 'javascript', 'sap', 'erp', 'hóa đơn điện tử', 'e-commerce',
        'thương mại điện tử', 'shopify', 'magento', 'cloud', 'security', 'bảo mật', 'open source', 'mã nguồn mở',
        'api', 'automation', 'tự động hóa', 'chuyển đổi số', 'startup',
    ];

    /**
     * @param  list<string>  $boost  owner supplied priority keywords
     */
    public function score(NewsItem $item, CarbonInterface $now, int $maxAgeHours, array $boost = []): int
    {
        $ageHours = max(0, $item->published_at ? $item->published_at->diffInMinutes($now, false) / 60 : 0);
        $freshness = 50 * max(0.0, 1 - $ageHours / max(1, $maxAgeHours));

        $weight = 10 * max(0, (int) ($item->source?->weight ?? 1));

        $relevance = min(50, $this->hits($item, self::KEYWORDS, 10, 5));
        $boosted = min(30, $this->hits($item, $boost, 10, 5));

        return (int) round($freshness + $weight + $relevance + $boosted);
    }

    /** @param  list<string>  $keywords */
    private function hits(NewsItem $item, array $keywords, int $titlePoints, int $excerptPoints): int
    {
        $title = mb_strtolower($item->title);
        $excerpt = mb_strtolower((string) $item->excerpt);
        $points = 0;

        foreach ($keywords as $keyword) {
            $keyword = mb_strtolower(trim($keyword));
            if ($keyword === '') {
                continue;
            }
            $pattern = '/(?<![\p{L}\p{N}])'.preg_quote($keyword, '/').'(?![\p{L}\p{N}])/u';
            if (preg_match($pattern, $title)) {
                $points += $titlePoints;
            } elseif (preg_match($pattern, $excerpt)) {
                $points += $excerptPoints;
            }
        }

        return $points;
    }
}
