<?php

/*
|--------------------------------------------------------------------------
| Daily news auto-poster
|--------------------------------------------------------------------------
|
| Defaults only. Every key below can be overridden from the admin page
| "Cấu hình tin tự động" (stored in the news_settings table); the database
| value wins over this file and this file wins over the raw environment.
| API keys are never stored here: the env value is only the fallback used
| when no key was saved from the admin page.
|
*/

return [
    // Kill switch for the whole feature (scheduler, command, queued run).
    'enabled' => (bool) env('NEWS_AUTOPUBLISH', true),

    // publish = go live immediately, draft = status pending for manual review.
    'mode' => env('NEWS_MODE', 'publish'),

    // Daily run window: the scheduler ticks every minute and runs once per day.
    'run_time' => env('NEWS_RUN_TIME', '09:00'),
    'timezone' => env('NEWS_TIMEZONE', 'Asia/Ho_Chi_Minh'),

    'posts_per_day' => (int) env('NEWS_POSTS_PER_DAY', 10),
    'per_source_cap' => (int) env('NEWS_PER_SOURCE_CAP', 3),
    'max_age_hours' => (int) env('NEWS_MAX_AGE_HOURS', 36),

    // Only the first N entries of each feed are stored per run.
    'ingest_per_source' => 40,
    // Extra candidates tried when the writer rejects or fails an item.
    'attempt_factor' => 2,
    'dedupe_days' => 14,
    'title_similarity' => 0.8,

    // Article length requested from the model; output below the floor is rejected.
    'min_words' => (int) env('NEWS_MIN_WORDS', 400),
    'max_words' => (int) env('NEWS_MAX_WORDS', 700),
    'reject_below_words' => 250,
    'max_source_overlap' => 0.35,

    'model' => env('NEWS_CLAUDE_MODEL', 'claude-opus-5-5'),
    // low | medium | high (ignored for models without an effort control).
    'effort' => env('NEWS_CLAUDE_EFFORT', 'low'),
    'max_tokens' => (int) env('NEWS_CLAUDE_MAX_TOKENS', 6000),

    'author_user_id' => env('NEWS_AUTHOR_USER_ID'),
    'default_category' => env('NEWS_DEFAULT_CATEGORY', 'Tin công nghệ'),

    // Extra, owner-editable guidance appended AFTER the fixed system rules.
    'extra_prompt' => '',
    'boost_keywords' => [],
    'blocked_keywords' => [],

    'images' => [
        // stock_first | generated_first | generated_only
        'mode' => env('NEWS_IMAGE_MODE', 'stock_first'),
        // 0-2 inline images besides the cover.
        'inline' => (int) env('NEWS_INLINE_IMAGES', 2),
        // Hotlinking or re-hosting the source article image is off by default (copyright).
        'use_source_image' => (bool) env('NEWS_USE_SOURCE_IMAGE', false),
        'width' => 1200,
        'height' => 630,
        'quality' => 82,
        'max_download_bytes' => 8 * 1024 * 1024,
    ],

    'http' => [
        'timeout' => 20,
        'user_agent' => env('NEWS_USER_AGENT', 'ToilamERPNewsBot/1.0 (+https://toilamerp.com)'),
        'max_feed_bytes' => 5 * 1024 * 1024,
    ],

    'site_name' => env('NEWS_SITE_NAME', 'toilamerp.com'),

    // Fallback credentials. The admin page can override each one (stored encrypted).
    'keys' => [
        'anthropic' => env('ANTHROPIC_API_KEY'),
        'unsplash' => env('UNSPLASH_ACCESS_KEY'),
        'pexels' => env('PEXELS_API_KEY'),
    ],
];
