<?php

namespace App\Filament\Customer\Resources\TaxInvoices\Pages;

use App\Filament\Customer\Resources\TaxInvoices\InvoiceExportActions;
use App\Filament\Customer\Resources\TaxInvoices\TaxInvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListTaxInvoices extends ListRecords
{
    protected static string $resource = TaxInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return InvoiceExportActions::header();
    }
}
