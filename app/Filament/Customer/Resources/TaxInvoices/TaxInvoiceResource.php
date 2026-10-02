<?php

namespace App\Filament\Customer\Resources\TaxInvoices;

use App\Filament\Customer\Resources\TaxInvoices\Pages\ListTaxInvoices;
use App\Models\TaxInvoice;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Read-only list of the active company's invoices cached from the GDT portal. */
class TaxInvoiceResource extends Resource
{
    protected static ?string $model = TaxInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Hóa đơn';

    protected static ?string $modelLabel = 'hóa đơn';

    protected static ?string $pluralModelLabel = 'hóa đơn';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    /** Without a valid license the cached invoice data stays hidden; the page shows the notice instead. */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = Filament::getTenant();

        return $tenant && $tenant->hasValidLicense() ? $query : $query->whereRaw('1 = 0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('issued_at')->label('Ngày lập')->date('d/m/Y')->sortable(),
                TextColumn::make('symbol')->label('Ký hiệu')->searchable(),
                TextColumn::make('number')->label('Số HĐ')->searchable(),
                TextColumn::make('seller_name')->label('Người bán')->description(fn (TaxInvoice $r) => $r->mst_seller)
                    ->searchable(['seller_name', 'mst_seller'])->wrap(),
                TextColumn::make('buyer_name')->label('Người mua')->description(fn (TaxInvoice $r) => $r->mst_buyer)
                    ->searchable(['buyer_name', 'mst_buyer'])->wrap()->toggleable(),
                TextColumn::make('total_before_tax')->label('Chưa thuế')->numeric(0, ',', '.')->alignEnd()->toggleable(),
                TextColumn::make('total_tax')->label('Thuế')->numeric(0, ',', '.')->alignEnd()->toggleable(),
                TextColumn::make('total_payment')->label('Thanh toán')->numeric(0, ',', '.')->alignEnd()->sortable(),
                TextColumn::make('source')->label('Nguồn')->badge()
                    ->formatStateUsing(fn (string $state) => $state === TaxInvoice::SOURCE_MTT ? 'Máy tính tiền' : 'Thường')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('direction')->label('Loại')->options([
                    TaxInvoice::DIRECTION_PURCHASE => 'Mua vào',
                    TaxInvoice::DIRECTION_SOLD => 'Bán ra',
                ])->default(TaxInvoice::DIRECTION_PURCHASE),
                Filter::make('period')->schema([
                    DatePicker::make('from')->label('Từ ngày')->native(false),
                    DatePicker::make('to')->label('Đến ngày')->native(false),
                ])->query(fn (Builder $query, array $data) => $query
                    ->when($data['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('issued_at', '>=', $d))
                    ->when($data['to'] ?? null, fn (Builder $q, $d) => $q->whereDate('issued_at', '<=', $d))),
            ])
            ->recordActions([
                InvoicePortalActions::detail(),
                InvoicePortalActions::preview(),
                InvoicePortalActions::downloadXml(),
            ])
            ->toolbarActions([InvoiceExportActions::bulk()])
            ->emptyStateHeading('Chưa có hóa đơn')
            ->emptyStateDescription('Kết nối cổng thuế rồi bấm "Tải từ cổng thuế" để lấy hóa đơn.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxInvoices::route('/'),
        ];
    }
}
