<?php

namespace App\Services\News\Writing;

use App\Models\NewsItem;
use App\Services\News\Content\HtmlSanitizer;
use App\Services\News\NewsSettings;
use Illuminate\Support\Str;

/**
 * Last gate before an article is saved: sanitises the body, enforces the limits the model
 * was asked to respect and refuses text that is too close to the source.
 */
class ArticleValidator
{
    private const SHINGLE = 5;

    public function __construct(
        private readonly HtmlSanitizer $sanitizer,
        private readonly NewsSettings $settings,
    ) {}

    /**
     * @return ArticleDraft cleaned draft (sanitised body, clipped SEO fields)
     *
     * @throws ArticleRejectedException with a human readable reason
     */
    public function validate(ArticleDraft $draft, NewsItem $item): ArticleDraft
    {
        if (! $draft->publishable) {
            throw new ArticleRejectedException($draft->rejectReason !== '' ? $draft->rejectReason : 'Mô hình đánh giá không nên đăng.');
        }

        $body = $this->sanitizer->sanitize($draft->bodyHtml);
        $slug = Str::slug($draft->slug !== '' ? $draft->slug : $draft->title);

        if ($draft->title === '' || $slug === '' || $body === '') {
            throw new ArticleRejectedException('Tiêu đề, slug hoặc nội dung trống.');
        }

        $text = $this->plainText($body);
        $words = $this->wordCount($text);
        $floor = min((int) $this->settings->get('reject_below_words'), (int) $this->settings->get('min_words'));
        if ($words < $floor) {
            throw new ArticleRejectedException("Bài quá ngắn ({$words} từ, tối thiểu {$floor}).");
        }

        $overlap = $this->overlap($text, trim($item->excerpt.' '.$item->content));
        if ($overlap > (float) $this->settings->get('max_source_overlap')) {
            throw new ArticleRejectedException(sprintf('Nội dung trùng nguồn quá nhiều (%.0f%%).', $overlap * 100));
        }

        return new ArticleDraft(
            publishable: true,
            rejectReason: '',
            title: Str::limit($draft->title, 250, ''),
            seoTitle: $this->clip($draft->seoTitle !== '' ? $draft->seoTitle : $draft->title, 60),
            metaDescription: $this->clip($draft->metaDescription, 155),
            slug: Str::limit($slug, 150, ''),
            keywords: array_slice(array_values(array_unique($draft->keywords)), 0, 8),
            tags: array_slice(array_values(array_unique(array_map(fn ($t) => Str::limit($t, 50, ''), $draft->tags))), 0, 4),
            bodyHtml: $body,
            imageQuery: $draft->imageQuery !== '' ? $draft->imageQuery : 'technology',
            imageAlt: $draft->imageAlt !== '' ? Str::limit($draft->imageAlt, 200, '') : $draft->title,
        );
    }

    /** Share of the article's (or the source's) word 5-grams that also occur in the other text. */
    public function overlap(string $article, string $source): float
    {
        $a = $this->shingles($article);
        $s = $this->shingles($source);
        if ($a === [] || $s === []) {
            return 0.0;
        }

        $common = count(array_intersect_key($a, $s));
        $articleInSource = $common / count($a);
        // A short source fully pasted into a long article barely moves the first ratio: check the reverse too.
        $sourceInArticle = count($s) >= 8 ? $common / count($s) : 0.0;

        return max($articleInSource, $sourceInArticle);
    }

    private function plainText(string $html): string
    {
        $spaced = preg_replace('#</(p|h2|h3|li|blockquote|figcaption)>|<br>#i', ' ', $html) ?? $html;

        return trim(html_entity_decode(strip_tags($spaced), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function wordCount(string $text): int
    {
        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }

    /** @return array<string, true> */
    private function shingles(string $text): array
    {
        $words = preg_split('/\s+/u', mb_strtolower(preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $set = [];
        for ($i = 0, $n = count($words) - self::SHINGLE + 1; $i < $n; $i++) {
            $set[implode(' ', array_slice($words, $i, self::SHINGLE))] = true;
        }

        return $set;
    }

    /** Clip at a word boundary so SEO fields never end mid-word. */
    private function clip(string $text, int $max): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $cut = mb_substr($text, 0, $max);
        $space = mb_strrpos($cut, ' ');

        return rtrim($space !== false && $space > $max * 0.6 ? mb_substr($cut, 0, $space) : $cut, ' ,;:-');
    }
}
