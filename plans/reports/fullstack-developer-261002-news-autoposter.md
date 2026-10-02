# News auto-poster: report

Branch `feat/news-autoposter` (worktree), not pushed/merged/deployed. 185 tests pass; the 2 failures (`ExampleTest`, `ProductAdminTest` marketplace page) also fail on a clean checkout of this worktree: `public/build/manifest.json` (Vite assets, git-ignored) does not exist here. Pint clean on all new files.

## What was built
- Tables: `news_sources`, `news_items` (+ `content` column for feed content:encoded), `news_runs` (+ `status`), `news_settings` (key/value, secrets encrypted). Models + factories.
- Services in `app/Services/News`: Feed (parser/fetcher), Selection (scorer/selector/title similarity), Writing (`ArticleWriter` interface, `ClaudeArticleWriter` on official `anthropic-ai/sdk` v0.54, prompt, validator, model/cost table), Content (`HtmlSanitizer`, `ArticleBodyBuilder`), Images (stock client, `CoverGenerator`, `ImageProcessor` via spatie/image, `ImageService`), Publishing (`PostPublisher`, `SitemapRefresher`), Pipeline (`NewsPipeline`, `DailyQuota`, `ScheduleGate`), `NewsSettings`, `ApiKeyTester`.
- `php artisan news:crawl {--dry-run} {--limit=} {--source=} {--scheduled}`; job `RunNewsCrawl`; `NewsSourceSeeder`.
- Schedule (routes/console.php): `news:crawl --scheduled` every minute, `withoutOverlapping`, `onOneServer`, `when(enabled)` (kill switch). Command runs once per day at/after configured time (catches up), no completed run today, 30 min retry throttle.
- Admin (group "Tin tự động"): page `/admin/cau-hinh-tin-tu-dong`, resources Nguồn tin (CRUD, toggle, last error), Tin đã thu thập (status filter, "Gỡ bài"), Nhật ký chạy (read only).
- Bundled font Be Vietnam Pro (SIL OFL 1.1, licence file in `resources/fonts`) for the generated cover; verified visually that Vietnamese diacritics render.

## Settings exposed on the admin page (DB > config/news.php > env)
Bật/tắt tổng; đăng tự động / lưu nháp (pending); giờ chạy HH:MM; múi giờ; tác giả; danh mục mặc định; số bài/ngày; tối đa bài/nguồn; tuổi tin tối đa; độ dài min/max từ; số ảnh trong bài 0-2; model (opus-5-5 / sonnet-5-5 / haiku-4-5); effort low/medium/high (hidden for haiku) with live daily cost estimate; extra prompt guidance (appended after fixed rules, fenced, 2000 char cap, closing reminder); boost keywords; blocked keywords/domains; image mode (stock first / generated first / generated only = never stock); API keys anthropic/unsplash/pexels (encrypted, write-only, "Đã cấu hình / Chưa cấu hình", Đổi key / Xóa key / Kiểm tra kết nối); header buttons "Chạy thử (dry-run)" and "Chạy ngay" (confirmation, queued `RunNewsCrawl`). Only `App\Models\User` (admin guard) can open it; customers are denied (tested).

## Feeds verified (real HTTP GET, 2026-10-02, valid XML with items)
Enabled: VietnamNet Công nghệ (36), VnExpress Số hóa (60), Tuổi Trẻ Nhịp sống số (50), hnrss.org/frontpage (20), dev.to (12), arXiv cs.AI (568), Latent Space (20), The Pragmatic Engineer (20), Import AI (20), Simon Willison atom (30), TechCrunch AI (19).
Disabled: VietnamNet thông-tin-truyền-thông (valid but ~1000 archive items, 1 MB, duplicates Công nghệ; reason in `last_error`).
Notes: VietnamNet Công nghệ's newest item was dated 2025-03, so it yields nothing under the 36h rule today. X/Twitter excluded (documented in seeder). No HTML scraping anywhere.
A live dry run over these feeds (in-memory SQLite) selected 10 items, wrote nothing.

## Env vars the owner must set
Required: `ANTHROPIC_API_KEY` (or save it in the admin page). Optional: `UNSPLASH_ACCESS_KEY`, `PEXELS_API_KEY` (without them every post gets the generated cover). Others have safe defaults and are listed in `.env.example`: `NEWS_AUTOPUBLISH`, `NEWS_MODE`, `NEWS_RUN_TIME`, `NEWS_TIMEZONE`, `NEWS_POSTS_PER_DAY`, `NEWS_PER_SOURCE_CAP`, `NEWS_MAX_AGE_HOURS`, `NEWS_CLAUDE_MODEL`, `NEWS_CLAUDE_EFFORT`, `NEWS_CLAUDE_MAX_TOKENS`, `NEWS_AUTHOR_USER_ID`, `NEWS_DEFAULT_CATEGORY`, `NEWS_IMAGE_MODE`, `NEWS_INLINE_IMAGES`, `NEWS_USE_SOURCE_IMAGE=false`.
Deploy steps: `migrate`, `db:seed --class=NewsSourceSeeder`, `storage:link` (already used by blog covers), cron `schedule:run` every minute, queue worker running (deploy already runs `queue:restart`).

## Estimated Claude cost, 10 posts/day
List prices: Opus 5.5 $4/$20 per MTok. Assumption per post: ~2.5k input, ~3.5k output (low effort, includes thinking), +30% for rejected/retried items. Opus 5.5 low: about $1.0/day (~$31/month); medium (5k output): about $1.4/day; Sonnet 5.5 low: about $0.5/day; Haiku 4.5: about $0.25/day. Estimates, not measured: no real API call was made. Prompt caching is enabled on the system block.

## Decisions / deviations
- Scheduler ticks every minute (owner's later requirement) instead of `dailyAt('09:00')`.
- `BlogsController::show` now only serves PUBLISHED posts (was any status). Without this "Gỡ bài" and draft mode would not actually hide the page. Small change in an existing public controller; revert if unwanted.
- Posts set `is_published` too (existing admin toggle/listing uses it).
- `news_items.content`, `news_runs.status` added beyond the requested columns.
- Candidates are de-duplicated against recent items and post titles at >= 0.8 similarity; the selector returns 2x the quota so rejected/failed items are replaced and 10 posts are still reached.
- Artifacts only in worktree: `.env` (copy of `.env.example`, git-ignored), vendor/ install. Pint was accidentally run on existing Filament files once and reverted.
- Shell note: PowerShell was blocked in this session, so php/composer ran via the ServBay `php.exe` directly (same binaries).
- Report written to the worktree copy of `plans/reports/` (the shared-checkout path was refused by the sandbox); it travels with the branch.

## Unresolved questions
1. Source block says "Bài viết được tổng hợp và biên tập lại bằng tiếng Việt từ nguồn trên." Do you want an explicit "có hỗ trợ AI" disclosure? (Not added.)
2. Stock-image terms: Unsplash asks for hotlinking in production apps; spec said download, so images are downloaded and re-hosted with credit. Confirm acceptable or apply for production API status.
3. English-source articles are paraphrased from title + excerpt (+ content:encoded when the feed has it). For paywalled/short feeds (Pragmatic Engineer, arXiv abstracts) the model will often answer `publishable:false`; consider disabling arXiv if rejection cost matters.
4. Real Claude/Unsplash/Pexels calls were never made (tests use fakes). First production run should be `news:crawl --limit=1` with a real key, then check output quality and the `max_tokens=6000` headroom at `medium` effort.
