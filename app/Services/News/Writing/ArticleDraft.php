<?php

namespace App\Services\News\Writing;

/** Schema-validated answer of the article writer. */
final class ArticleDraft
{
    /**
     * @param  list<string>  $keywords
     * @param  list<string>  $tags
     */
    public function __construct(
        public readonly bool $publishable,
        public readonly string $rejectReason,
        public readonly string $title,
        public readonly string $seoTitle,
        public readonly string $metaDescription,
        public readonly string $slug,
        public readonly array $keywords,
        public readonly array $tags,
        public readonly string $bodyHtml,
        public readonly string $imageQuery,
        public readonly string $imageAlt,
    ) {}

    public static function rejected(string $reason): self
    {
        return new self(false, $reason, '', '', '', '', [], [], '', '', '');
    }

    /**
     * Strict conversion of the decoded JSON: any missing key or wrong type is a writer error,
     * never silently defaulted.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ArticleWriterException
     */
    public static function fromArray(array $data): self
    {
        $string = function (string $key) use ($data): string {
            if (! array_key_exists($key, $data) || ! is_string($data[$key])) {
                throw new ArticleWriterException("Phản hồi thiếu hoặc sai kiểu trường [{$key}].");
            }

            return trim($data[$key]);
        };
        $list = function (string $key) use ($data): array {
            if (! isset($data[$key]) || ! is_array($data[$key])) {
                throw new ArticleWriterException("Phản hồi thiếu hoặc sai kiểu trường [{$key}].");
            }

            return array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : '', $data[$key])));
        };

        if (! isset($data['publishable']) || ! is_bool($data['publishable'])) {
            throw new ArticleWriterException('Phản hồi thiếu trường [publishable].');
        }

        return new self(
            publishable: $data['publishable'],
            rejectReason: $string('reject_reason'),
            title: $string('title'),
            seoTitle: $string('seo_title'),
            metaDescription: $string('meta_description'),
            slug: $string('slug'),
            keywords: $list('keywords'),
            tags: $list('tags'),
            bodyHtml: $string('body_html'),
            imageQuery: $string('image_query'),
            imageAlt: $string('image_alt'),
        );
    }
}
