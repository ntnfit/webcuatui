<?php

namespace App\Filament\Customer\Pages\Tenancy;

use App\Models\Company;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Thêm công ty';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(CompanyFormFields::components());
    }

    protected function handleRegistration(array $data): Model
    {
        $customer = auth('customer')->user();

        return DB::transaction(function () use ($data, $customer) {
            $company = Company::create([...$data, 'owner_id' => $customer->getKey()]);
            $company->members()->attach($customer->getKey(), ['role' => 'owner']);

            return $company;
        });
    }
}
