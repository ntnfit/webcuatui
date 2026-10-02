<?php

namespace App\Services\Gdt\Export;

use App\Models\TaxExportRun;
use App\Models\TaxInvoice;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\GdtSessionService;
use App\Services\Gdt\InvoiceSearch;
use App\Services\Gdt\InvoiceSyncService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** Executes one queued export run end to end and tells the requesting customer how it went. */
class ExportRunner
{
    public const DISK = 'local';

    public const RETENTION_DAYS = 7;

    public function __construct(
        private readonly GdtSessionService $sessions,
        private readonly InvoiceSyncService $sync,
        private readonly InvoiceXmlExporter $xml,
        private readonly InvoiceExcelExporter $excel,
    ) {}

    public function execute(TaxExportRun $run): void
    {
        $run->update(['status' => TaxExportRun::STATUS_RUNNING, 'error' => null]);

        try {
            $search = InvoiceSearch::fromArray($run->filters);
            $invoices = $this->invoicesFor($run, $search);

            if ($invoices->isEmpty()) {
                throw new GdtException('Không có hóa đơn nào trong kỳ đã chọn. Hãy tải hóa đơn từ cổng thuế trước.');
            }

            if ($invoices->count() > ExportRequestService::MAX_INVOICES) {
                throw new GdtException('Quá '.ExportRequestService::MAX_INVOICES.' hóa đơn, vui lòng thu hẹp kỳ hoặc lọc theo MST đối tác.');
            }

            $relative = "tax-exports/{$run->company_id}/{$run->id}-".$this->fileName($run);
            $absolute = $this->prepare($relative);

            $note = match ($run->type) {
                TaxExportRun::TYPE_EXCEL => $this->buildExcel($run, $invoices, $search, $absolute),
                TaxExportRun::TYPE_XML_ZIP => $this->buildXmlZip($run, $invoices, $absolute),
            };

            $run->update([
                'status' => TaxExportRun::STATUS_DONE,
                'file_path' => $relative,
                'expires_at' => now()->addDays(self::RETENTION_DAYS),
                'error' => $note,
            ]);
            $this->notify($run, true, 'Xuất file hoàn tất'.($note ? " ($note)" : '').'.');
        } catch (Throwable $e) {
            $message = $e instanceof GdtException || $e instanceof \InvalidArgumentException
                ? $e->getMessage()
                : 'Lỗi hệ thống khi xuất file.';
            $run->update(['status' => TaxExportRun::STATUS_FAILED, 'error' => $message]);
            $this->notify($run, false, $message);

            if (! $e instanceof GdtException && ! $e instanceof \InvalidArgumentException) {
                report($e);
            }
        }
    }

    /** @return Collection<int, TaxInvoice> */
    private function invoicesFor(TaxExportRun $run, InvoiceSearch $search): Collection
    {
        $query = TaxInvoice::withoutGlobalScopes()
            ->where('company_id', $run->company_id)
            ->where('direction', $search->direction)
            ->whereBetween('issued_at', [$search->from->copy()->startOfDay(), $search->to->copy()->endOfDay()])
            ->orderBy('issued_at', 'desc')
            ->orderBy('id');

        if ($search->counterpartMst) {
            $query->where($search->direction === TaxInvoice::DIRECTION_SOLD ? 'mst_buyer' : 'mst_seller', $search->counterpartMst);
        }

        if (! empty($run->filters['ids'])) {
            $query->whereIn('id', $run->filters['ids']);
        }

        return $query->get();
    }

    private function buildExcel(TaxExportRun $run, Collection $invoices, InvoiceSearch $search, string $path): ?string
    {
        $missing = $invoices->filter(fn (TaxInvoice $i) => ! $i->detail);
        $failed = 0;

        if ($missing->isNotEmpty()) {
            $this->sessions->run($run->company, function (GdtClient $client) use ($missing, &$failed) {
                foreach ($missing as $invoice) {
                    try {
                        $this->sync->loadDetail($invoice, $client);
                    } catch (GdtException $e) {
                        if ($e->status === 401) {
                            throw $e;
                        }
                        $failed++;
                    }
                }
            });
        }

        $this->excel->write($invoices, $search, $path);

        return $failed > 0 ? "{$failed} hóa đơn thiếu chi tiết sản phẩm" : null;
    }

    private function buildXmlZip(TaxExportRun $run, Collection $invoices, string $path): ?string
    {
        $result = $this->sessions->run($run->company, fn (GdtClient $client) => $this->xml->buildZip($invoices, $client, $path));

        if ($result['added'] === 0) {
            throw new GdtException('Không tải được XML của hóa đơn nào.');
        }

        return $result['failed'] ? count($result['failed']).' hóa đơn không tải được XML' : null;
    }

    private function fileName(TaxExportRun $run): string
    {
        return $run->type === TaxExportRun::TYPE_EXCEL ? 'hoadon.xlsx' : 'hoadon_xml.zip';
    }

    private function prepare(string $relative): string
    {
        $disk = Storage::disk(self::DISK);
        $disk->makeDirectory(dirname($relative));

        return $disk->path($relative);
    }

    private function notify(TaxExportRun $run, bool $ok, string $body): void
    {
        if (! $run->customer) {
            return;
        }

        $notification = Notification::make()->title($ok ? 'Xuất hóa đơn thành công' : 'Xuất hóa đơn thất bại')->body($body);
        ($ok ? $notification->success() : $notification->danger())->sendToDatabase($run->customer);
    }
}
