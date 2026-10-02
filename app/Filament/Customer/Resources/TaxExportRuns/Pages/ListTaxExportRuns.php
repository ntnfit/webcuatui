<?php

namespace App\Filament\Customer\Resources\TaxExportRuns\Pages;

use App\Filament\Customer\Resources\TaxExportRuns\TaxExportRunResource;
use Filament\Resources\Pages\ListRecords;

class ListTaxExportRuns extends ListRecords
{
    protected static string $resource = TaxExportRunResource::class;
}
