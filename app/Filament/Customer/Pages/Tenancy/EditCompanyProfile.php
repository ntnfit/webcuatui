<?php

namespace App\Filament\Customer\Pages\Tenancy;

use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Thông tin công ty';
    }

    /** Only the owner may rename or change the tax code of a company. */
    public static function canView(Model $tenant): bool
    {
        $customer = auth('customer')->user();

        return $customer !== null && $tenant->isOwnedBy($customer);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(CompanyFormFields::components());
    }
}
