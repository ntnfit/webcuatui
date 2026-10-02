<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\TaxInvoice;

/**
 * Invoices are synced from the GDT portal and never edited by hand, so only reading is allowed,
 * and only for members of a company holding a valid Tax license.
 */
class TaxInvoicePolicy
{
    public function viewAny(Customer $customer): bool
    {
        return true;
    }

    public function view(Customer $customer, TaxInvoice $invoice): bool
    {
        return $customer->canAccessTenant($invoice->company) && $invoice->company->hasValidLicense();
    }

    public function create(Customer $customer): bool
    {
        return false;
    }

    public function update(Customer $customer, TaxInvoice $invoice): bool
    {
        return false;
    }

    public function delete(Customer $customer, TaxInvoice $invoice): bool
    {
        return false;
    }
}
