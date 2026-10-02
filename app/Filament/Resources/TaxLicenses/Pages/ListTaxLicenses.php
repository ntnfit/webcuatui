<?php

namespace App\Filament\Resources\TaxLicenses\Pages;

use App\Filament\Resources\TaxLicenses\TaxLicenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxLicenses extends ListRecords
{
    protected static string $resource = TaxLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
