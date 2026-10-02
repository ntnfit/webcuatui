<?php

namespace App\Services\News\Pipeline;

/** What one pipeline execution may do. */
final class RunOptions
{
    public function __construct(
        public readonly bool $dryRun = false,
        public readonly ?int $limit = null,
        /** Restrict the run to one source: numeric id or part of its name. */
        public readonly ?string $source = null,
    ) {}
}
