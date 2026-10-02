<?php

namespace App\Services\News\Pipeline;

use App\Models\NewsRun;
use App\Services\News\NewsSettings;
use Carbon\CarbonImmutable;
use Throwable;

/**
 * Decides whether the every-minute scheduler tick should start the daily run: the feature is on,
 * the configured time of day has passed (so a missed minute is caught up), no run completed yet
 * today and no other run started in the last half hour (overlap and retry throttle).
 */
class ScheduleGate
{
    private const RETRY_AFTER_MINUTES = 30;

    public function __construct(
        private readonly NewsSettings $settings,
        private readonly DailyQuota $quota,
    ) {}

    public function shouldRun(?CarbonImmutable $now = null): bool
    {
        $now ??= now()->toImmutable();

        if (! $this->settings->enabled()) {
            return false;
        }

        $local = $now->setTimezone($this->quota->timezone());
        $runAt = $this->runTime($local);
        if ($local->lessThan($runAt)) {
            return false;
        }

        $appTz = config('app.timezone');

        $doneToday = NewsRun::where('status', NewsRun::STATUS_COMPLETED)
            ->where('started_at', '>=', $this->quota->startOfToday($now))
            ->exists();
        if ($doneToday) {
            return false;
        }

        return ! NewsRun::where('started_at', '>=', $now->subMinutes(self::RETRY_AFTER_MINUTES)->setTimezone($appTz))->exists();
    }

    /** Safe variant for the scheduler filter: a missing table or bad setting must not break `schedule:run`. */
    public function shouldRunSafely(): bool
    {
        try {
            return $this->shouldRun();
        } catch (Throwable) {
            return false;
        }
    }

    private function runTime(CarbonImmutable $local): CarbonImmutable
    {
        $time = (string) $this->settings->get('run_time');
        if (! preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time, $m)) {
            $m = [null, '09', '00'];
        }

        return $local->setTime((int) $m[1], (int) $m[2]);
    }
}
