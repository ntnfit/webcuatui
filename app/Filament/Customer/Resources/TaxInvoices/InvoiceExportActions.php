<?php

namespace App\Filament\Customer\Resources\TaxInvoices;

use App\Models\Company;
use App\Models\Customer;
use App\Models\TaxExportRun;
use App\Models\TaxInvoice;
use App\Services\Gdt\Export\ExportRequestService;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\InvoiceSearch;
use App\Services\Gdt\InvoiceSyncService;
use App\Services\Gdt\TaxRateLimiter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use RuntimeException;

/** List-page actions: pull invoices from the portal and queue Excel/XML exports. */
final class InvoiceExportActions
{
    private const TYPES = [
        TaxExportRun::TYPE_EXCEL => ['Xuất Excel', 'heroicon-o-table-cells'],
        TaxExportRun::TYPE_XML_ZIP => ['Xuất XML (zip)', 'heroicon-o-archive-box-arrow-down'],
    ];

    /** @return list<Action> */
    public static function header(): array
    {
        $actions = [
            Action::make('sync')
                ->label('Tải từ cổng thuế')
                ->icon('heroicon-o-cloud-arrow-down')
                ->modalHeading('Tải hóa đơn từ cổng thuế')
                ->schema(InvoiceSearchFields::components())
                ->action(fn (array $data) => self::sync($data)),
        ];

        foreach (self::TYPES as $type => [$label, $icon]) {
            $actions[] = Action::make("export_{$type}")
                ->label($label)
                ->icon($icon)
                ->color('gray')
                ->modalHeading($label.' các hóa đơn đã tải')
                ->modalDescription('File được tạo ở nền; bạn sẽ nhận thông báo khi hoàn tất.')
                ->schema(InvoiceSearchFields::components())
                ->action(fn (array $data) => self::queue($type, InvoiceSearchFields::toSearch($data)));
        }

        return $actions;
    }

    public static function bulk(): BulkActionGroup
    {
        $actions = [];

        foreach (self::TYPES as $type => [$label, $icon]) {
            $actions[] = BulkAction::make("export_{$type}")
                ->label($label.' mục đã chọn')
                ->icon($icon)
                ->deselectRecordsAfterCompletion()
                ->action(fn (Collection $records) => self::queueSelection($type, $records));
        }

        return BulkActionGroup::make($actions);
    }

    /** @param array<string, mixed> $data */
    private static function sync(array $data): void
    {
        $company = self::company();

        try {
            $search = InvoiceSearchFields::toSearch($data);
            app(TaxRateLimiter::class)->hit('sync', $company, self::customer());
            $result = app(InvoiceSyncService::class)->sync($company, $search);
        } catch (GdtException $e) {
            InvoicePortalActions::failure($e);

            return;
        } catch (InvalidArgumentException|RuntimeException $e) {
            Notification::make()->title('Không thể tải hóa đơn')->body($e->getMessage())->danger()->send();

            return;
        }

        $notification = Notification::make()->title("Đã tải {$result['count']} hóa đơn")->success();
        if ($result['warnings']) {
            $notification->body(implode("\n", $result['warnings']));
        }
        $notification->send();
    }

    /** @param Collection<int, TaxInvoice> $records */
    private static function queueSelection(string $type, Collection $records): void
    {
        $directions = $records->pluck('direction')->unique();

        if ($records->isEmpty() || $directions->count() !== 1 || $records->contains(fn ($r) => $r->issued_at === null)) {
            Notification::make()->title('Hãy chọn hóa đơn cùng loại (mua vào hoặc bán ra).')->danger()->send();

            return;
        }

        try {
            $search = new InvoiceSearch($directions->first(), $records->min('issued_at'), $records->max('issued_at'));
        } catch (InvalidArgumentException $e) {
            Notification::make()->title('Không thể xuất')->body($e->getMessage())->danger()->send();

            return;
        }

        self::queue($type, $search, $records->modelKeys());
    }

    /** @param list<int>|null $ids */
    private static function queue(string $type, InvoiceSearch $search, ?array $ids = null): void
    {
        try {
            app(ExportRequestService::class)->request(self::company(), self::customer(), $type, $search, $ids);
        } catch (InvalidArgumentException|RuntimeException $e) {
            Notification::make()->title('Không thể xuất file')->body($e->getMessage())->danger()->send();

            return;
        }

        Notification::make()->title('Đã đưa vào hàng đợi')
            ->body('Theo dõi tại mục "Lịch sử xuất file"; bạn sẽ nhận thông báo khi xong.')->success()->send();
    }

    private static function company(): Company
    {
        /** @var Company */
        return Filament::getTenant();
    }

    private static function customer(): Customer
    {
        /** @var Customer */
        return Filament::auth()->user();
    }
}
