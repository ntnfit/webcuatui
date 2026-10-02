<?php

namespace App\Services\News\Pipeline;

use App\Models\NewsItem;
use App\Services\News\NewsSettings;
use Carbon\CarbonImmutable;
use DateTimeZone;

/** Counts how many posts the bot already created today so reruns never exceed the daily cap. */
class DailyQuota
{
    public function __construct(private readonly NewsSettings $settings) {}

    public function timezone(): string
    {
        $tz = (string) $this->settings->get('timezone');

        return in_array($tz, DateTimeZone::listIdentifiers(), true) ? $tz : 'Asia/Ho_Chi_Minh';
    }

    /** Start of the current day in the configured timezone, expressed in the application timezone for queries. */
    public function startOfToday(?CarbonImmutable $now = null): CarbonImmutable
    {
        return ($now ?? now()->toImmutable())
            ->setTimezone($this->timezone())
            ->startOfDay()
            ->setTimezone(config('app.timezone'));
    }

    public function createdToday(?CarbonImmutable $now = null): int
    {
        $start = $this->startOfToday($now);

        return NewsItem::query()
            ->where('status', NewsItem::STATUS_PUBLISHED)
            ->whereHas('post', fn ($q) => $q->where('created_at', '>=', $start))
            ->count();
    }

    public function remaining(?CarbonImmutable $now = null): int
    {
        return max(0, (int) $this->settings->get('posts_per_day') - $this->createdToday($now));
    }
}
