<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Customer;

class CompanyPolicy
{
    public function viewAny(Customer $customer): bool
    {
        return true;
    }

    public function view(Customer $customer, Company $company): bool
    {
        return $company->hasMember($customer);
    }

    public function create(Customer $customer): bool
    {
        return true;
    }

    public function update(Customer $customer, Company $company): bool
    {
        return $company->isOwnedBy($customer);
    }

    public function delete(Customer $customer, Company $company): bool
    {
        return $company->isOwnedBy($customer);
    }
}
