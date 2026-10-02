<?php

namespace Database\Seeders;

use App\Models\NewsSource;
use Illuminate\Database\Seeder;

/**
 * Feeds read by the news auto-poster.
 *
 * Policy, kept deliberately narrow so the site stays on the right side of copyright and ToS:
 *  - Only official RSS / Atom feeds are read. No HTML page is ever scraped.
 *  - X/Twitter is NOT a source: it has no free API or feed that is safe to use under its ToS.
 *  - Each feed below was fetched with a real HTTP GET on 2026-10-02 and returned valid XML with
 *    items. Anything that is not worth reading stays in the table disabled, with the reason in
 *    last_error, so the owner can see why from the admin page.
 *
 * Idempotent: running it again updates the rows in place (matched by URL) and never touches
 * the enabled flag or weight an admin changed afterwards.
 */
class NewsSourceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sources() as $source) {
            $existing = NewsSource::where('url', $source['url'])->first();

            if ($existing) {
                $existing->update(['name' => $source['name'], 'type' => $source['type'], 'language' => $source['language']]);

                continue;
            }

            NewsSource::create($source + ['last_error' => null]);
        }
    }

    /** @return list<array<string, mixed>> */
    private function sources(): array
    {
        $feed = fn (string $name, string $url, string $language, int $weight, string $type = 'rss') => [
            'name' => $name, 'type' => $type, 'url' => $url, 'language' => $language, 'weight' => $weight, 'enabled' => true,
        ];

        return [
            $feed('VietnamNet - Công nghệ', 'https://vietnamnet.vn/rss/cong-nghe.rss', 'vi', 2),
            $feed('VnExpress - Số hóa', 'https://vnexpress.net/rss/so-hoa.rss', 'vi', 2),
            $feed('Tuổi Trẻ - Nhịp sống số', 'https://tuoitre.vn/rss/nhip-song-so.rss', 'vi', 2),
            $feed('Hacker News - Front page', 'https://hnrss.org/frontpage', 'en', 2),
            $feed('DEV Community', 'https://dev.to/feed', 'en', 1),
            $feed('arXiv cs.AI', 'https://rss.arxiv.org/rss/cs.AI', 'en', 1),
            $feed('Latent Space', 'https://www.latent.space/feed', 'en', 2),
            $feed('The Pragmatic Engineer', 'https://newsletter.pragmaticengineer.com/feed', 'en', 1),
            $feed('Import AI', 'https://importai.substack.com/feed', 'en', 2),
            $feed("Simon Willison's Weblog", 'https://simonwillison.net/atom/everything/', 'en', 2, 'atom'),
            $feed('TechCrunch - AI', 'https://techcrunch.com/category/artificial-intelligence/feed/', 'en', 2),
            [
                'name' => 'VietnamNet - Thông tin truyền thông',
                'type' => 'rss',
                'url' => 'https://vietnamnet.vn/rss/thong-tin-truyen-thong.rss',
                'language' => 'vi',
                'weight' => 1,
                'enabled' => false,
                'last_error' => 'Tắt khi kiểm tra: feed trả về khoảng 1000 mục lưu trữ (1 MB), trùng nội dung với mục Công nghệ.',
            ],
        ];
    }
}
