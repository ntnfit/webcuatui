<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\TaxExportRun;

class TaxExportRunPolicy
{
    public function viewAny(Customer $customer): bool
    {
        return true;
    }

    public function view(Customer $customer, TaxExportRun $run): bool
    {
        return $customer->canAccessTenant($run->company);
    }

    public function create(Customer $customer): bool
    {
        return false;
    }

    public function update(Customer $customer, TaxExportRun $run): bool
    {
        return false;
    }

    public function delete(Customer $customer, TaxExportRun $run): bool
    {
        return $customer->canAccessTenant($run->company);
    }
}
