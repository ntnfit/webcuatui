<?php

namespace App\Services\News\Pipeline;

use App\Models\NewsItem;
use App\Models\NewsRun;
use Illuminate\Support\Collection;

/** Outcome of one pipeline execution. */
final class RunResult
{
    /** @param  Collection<int, NewsItem>  $preview  items a dry run would publish */
    public function __construct(
        public readonly ?NewsRun $run,
        public readonly Collection $preview,
        /** Set when the run could not start or crashed; null for a normal finish. */
        public readonly ?string $error = null,
        public readonly int $remainingQuota = 0,
    ) {}

    public function failed(): bool
    {
        return $this->error !== null;
    }

    public static function aborted(?NewsRun $run, string $error): self
    {
        return new self($run, collect(), $error);
    }
}
