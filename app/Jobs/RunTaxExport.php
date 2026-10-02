<?php

namespace App\Jobs;

use App\Models\TaxExportRun;
use App\Services\Gdt\Export\ExportRunner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/** Builds one export file in the background so the web request never waits on the portal. */
class RunTaxExport implements ShouldQueue
{
    use Queueable;

    /** A retry would re-hit the portal with a possibly expired token; the user can request again. */
    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(public readonly int $runId) {}

    public function handle(ExportRunner $runner): void
    {
        $run = TaxExportRun::withoutGlobalScopes()->with(['company', 'customer'])->find($this->runId);

        if ($run === null || $run->status !== TaxExportRun::STATUS_PENDING) {
            return;
        }

        $runner->execute($run);
    }

    public function failed(\Throwable $e): void
    {
        TaxExportRun::withoutGlobalScopes()->whereKey($this->runId)->update([
            'status' => TaxExportRun::STATUS_FAILED,
            'error' => 'Tiến trình xuất file bị dừng bất ngờ.',
        ]);
    }
}
