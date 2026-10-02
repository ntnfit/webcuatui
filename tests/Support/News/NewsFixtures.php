<?php

namespace Tests\Support\News;

use App\Models\User;
use App\Services\News\Writing\ArticleDraft;
use App\Services\News\Writing\ArticleWriter;
use Illuminate\Support\Facades\Http;

/** Builders shared by the news auto-poster tests. */
class NewsFixtures
{
    /** RSS 2.0 document. Each item: title, url, [date], [description], [content], [image]. */
    public static function rss(array $items): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/"><channel><title>Test</title>';
        foreach ($items as $i) {
            $xml .= '<item><title>'.htmlspecialchars($i['title']).'</title><link>'.htmlspecialchars($i['url']).'</link><guid>'.htmlspecialchars($i['guid'] ?? $i['url']).'</guid>'
                .'<pubDate>'.($i['date'] ?? now()->subHour()->toRfc2822String()).'</pubDate>'
                .'<description><![CDATA['.($i['description'] ?? 'A short English summary of the article about AI and programming.').']]></description>'
                .(isset($i['content']) ? '<content:encoded><![CDATA['.$i['content'].']]></content:encoded>' : '')
                .(isset($i['image']) ? '<media:content url="'.$i['image'].'" medium="image"/>' : '')
                .'</item>';
        }

        return $xml.'</channel></rss>';
    }

    /** A publishable Vietnamese draft with about $words words and no overlap with English sources. */
    public static function draft(array $overrides = [], int $words = 450): ArticleDraft
    {
        $sentence = fn (int $n) => 'Đây là câu số '.$n.' phân tích tác động của xu hướng này đối với doanh nghiệp Việt Nam và đội ngũ phát triển phần mềm.';
        $paragraphs = '';
        for ($n = 1; $n * 20 <= $words; $n++) {
            $paragraphs .= '<p>'.$sentence($n).'</p>';
            if ($n % 5 === 0) {
                $paragraphs .= '<h2>Phần '.($n / 5).'</h2>';
            }
        }

        $base = [
            'publishable' => true,
            'rejectReason' => '',
            'title' => 'Tin công nghệ nổi bật hôm nay',
            'seoTitle' => 'Tin công nghệ nổi bật: AI và lập trình',
            'metaDescription' => 'Tóm tắt và phân tích tin công nghệ nổi bật dành cho doanh nghiệp và lập trình viên Việt Nam.',
            'slug' => 'tin-cong-nghe-noi-bat',
            'keywords' => ['tin công nghệ', 'AI', 'lập trình', 'SAP', 'ERP'],
            'tags' => ['AI', 'Lập trình'],
            'bodyHtml' => '<h2>Tổng quan</h2>'.$paragraphs.'<h2>Câu hỏi thường gặp</h2><h3>Có quan trọng không?</h3><p>Có.</p>',
            'imageQuery' => 'artificial intelligence',
            'imageAlt' => 'Minh họa trí tuệ nhân tạo',
        ];
        $d = $overrides + $base;

        return new ArticleDraft(...$d);
    }

    /** JSON object Claude would return for a publishable article (snake_case schema keys). */
    public static function payload(array $overrides = []): array
    {
        return $overrides + [
            'publishable' => true, 'reject_reason' => '', 'title' => 'Tiêu đề', 'seo_title' => 'SEO', 'meta_description' => 'Mô tả',
            'slug' => 'tieu-de', 'keywords' => ['a'], 'tags' => ['b'], 'body_html' => '<p>x</p>', 'image_query' => 'ai', 'image_alt' => 'alt',
        ];
    }

    public static function admin(): User
    {
        return User::factory()->create();
    }

    public static function bindWriter(?FakeArticleWriter $writer = null): FakeArticleWriter
    {
        $writer ??= new FakeArticleWriter;
        app()->instance(ArticleWriter::class, $writer);

        return $writer;
    }

    /** Fake one feed URL with $items and everything else with a 404. */
    public static function fakeFeed(string $url, array $items): void
    {
        Http::fake([$url => Http::response(self::rss($items), 200, ['Content-Type' => 'application/rss+xml'])]);
    }
}
