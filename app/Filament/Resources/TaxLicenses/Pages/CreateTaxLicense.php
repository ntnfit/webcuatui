<?php

namespace App\Filament\Resources\TaxLicenses\Pages;

use App\Filament\Resources\TaxLicenses\TaxLicenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxLicense extends CreateRecord
{
    protected static string $resource = TaxLicenseResource::class;

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
