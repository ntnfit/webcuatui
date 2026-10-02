<?php

namespace App\Filament\Customer\Resources\TaxInvoices;

use App\Filament\Customer\Pages\ConnectGdt;
use App\Models\TaxInvoice;
use App\Services\Gdt\Export\InvoiceHtmlRenderer;
use App\Services\Gdt\Export\InvoiceXmlExporter;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\GdtSessionService;
use App\Services\Gdt\InvoiceSyncService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/** Row actions that call the portal on demand: line items, official HTML view and single XML download. */
final class InvoicePortalActions
{
    private const PREVIEW_TTL_SECONDS = 600;

    public static function detail(): Action
    {
        return Action::make('detail')
            ->label('Chi tiết')
            ->icon('heroicon-o-eye')
            ->authorize('view')
            ->modalHeading(fn (TaxInvoice $record) => "Hóa đơn {$record->symbol}-{$record->number}")
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Đóng')
            ->modalContent(fn (TaxInvoice $record) => self::guarded($record, function (GdtClient $client) use ($record) {
                $invoice = app(InvoiceSyncService::class)->loadDetail($record, $client);

                return self::frame(app(InvoiceHtmlRenderer::class)->renderFromData($invoice));
            }));
    }

    public static function preview(): Action
    {
        return Action::make('preview')
            ->label('Xem / In PDF')
            ->icon('heroicon-o-printer')
            ->authorize('view')
            ->modalHeading(fn (TaxInvoice $record) => "Hóa đơn {$record->symbol}-{$record->number}")
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Đóng')
            ->modalContent(fn (TaxInvoice $record) => self::guarded($record, function (GdtClient $client) use ($record) {
                $html = Cache::remember(
                    "tax-invoice-html:{$record->company_id}:{$record->getKey()}",
                    self::PREVIEW_TTL_SECONDS,
                    function () use ($record, $client) {
                        $invoice = app(InvoiceSyncService::class)->loadDetail($record, $client);

                        return app(InvoiceHtmlRenderer::class)->render($invoice, $client);
                    },
                );

                return self::frame($html, printable: true);
            }));
    }

    public static function downloadXml(): Action
    {
        return Action::make('xml')
            ->label('Tải XML')
            ->icon('heroicon-o-arrow-down-tray')
            ->authorize('view')
            ->action(function (TaxInvoice $record) {
                try {
                    $file = app(GdtSessionService::class)->run(
                        $record->company,
                        fn (GdtClient $client) => app(InvoiceXmlExporter::class)->single($record, $client),
                    );
                } catch (GdtException $e) {
                    self::failure($e);

                    return null;
                }

                return response()->streamDownload(fn () => print ($file['content']), $file['name'], ['Content-Type' => $file['mime']]);
            });
    }

    /**
     * Runs a portal call for the record's company and turns failures into an inline message.
     *
     * @param  callable(GdtClient): HtmlString  $callback
     */
    private static function guarded(TaxInvoice $record, callable $callback): HtmlString
    {
        try {
            return app(GdtSessionService::class)->run($record->company, $callback);
        } catch (GdtException $e) {
            return new HtmlString('<p style="color:#b91c1c">'.e($e->getMessage()).'</p>');
        }
    }

    /**
     * Sandboxed frame: no scripts run in the invoice markup. `allow-same-origin` only lets the
     * surrounding page open the print dialog for the frame.
     */
    private static function frame(string $html, bool $printable = false): HtmlString
    {
        $id = 'gdt-frame-'.bin2hex(random_bytes(4));
        $button = $printable
            ? '<p style="margin-bottom:8px"><button type="button" class="fi-btn" style="padding:6px 12px;border:1px solid #d1d5db;border-radius:6px" '
                ."onclick=\"document.getElementById('{$id}').contentWindow.print()\">In / Lưu PDF</button></p>"
            : '';

        return new HtmlString(
            $button.'<iframe id="'.$id.'" sandbox="allow-same-origin" style="width:100%;height:70vh;border:1px solid #e5e7eb;background:#fff" srcdoc="'.e($html).'"></iframe>'
        );
    }

    /** Toast for a failed portal call; an expired session offers a shortcut to reconnect. */
    public static function failure(GdtException $e): void
    {
        $notification = Notification::make()->title('Lỗi cổng thuế')->body($e->getMessage())->danger();

        if ($e->status === 401) {
            $notification->actions([
                Action::make('reconnect')->label('Kết nối lại')->url(ConnectGdt::getUrl()),
            ]);
        }

        $notification->send();
    }
}
