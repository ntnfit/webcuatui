<?php

namespace App\Filament\Customer\Resources\TaxInvoices;

use App\Services\Gdt\InvoiceSearch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

/** Lookup/export form shared by the sync and export actions. */
final class InvoiceSearchFields
{
    /** @return array<int, Component> */
    public static function components(): array
    {
        return [
            Select::make('direction')->label('Loại hóa đơn')->options([
                'purchase' => 'Mua vào',
                'sold' => 'Bán ra',
            ])->default('purchase')->required(),
            DatePicker::make('from')->label('Từ ngày')->default(now()->subDays(29))->native(false)
                ->maxDate(now())->required(),
            DatePicker::make('to')->label('Đến ngày')->default(now())->native(false)
                ->maxDate(now())->required()->afterOrEqual('from')
                ->helperText('Khoảng tra cứu tối đa '.InvoiceSearch::MAX_DAYS.' ngày.'),
            TextInput::make('counterpart_mst')->label('MST đối tác (tùy chọn)')->maxLength(14)
                ->regex('/^[0-9-]{10,14}$/'),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function toSearch(array $data): InvoiceSearch
    {
        return InvoiceSearch::fromArray($data);
    }
}
