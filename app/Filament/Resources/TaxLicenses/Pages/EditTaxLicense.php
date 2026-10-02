<?php

namespace App\Filament\Resources\TaxLicenses\Pages;

use App\Filament\Resources\TaxLicenses\TaxLicenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaxLicense extends EditRecord
{
    protected static string $resource = TaxLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
