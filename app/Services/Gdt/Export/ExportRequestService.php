<?php

namespace App\Services\Gdt\Export;

use App\Jobs\RunTaxExport;
use App\Models\Company;
use App\Models\Customer;
use App\Models\TaxExportRun;
use App\Services\Gdt\InvoiceSearch;
use App\Services\Gdt\TaxRateLimiter;
use RuntimeException;

/** Validates an export request, applies rate limits and queues the background job. */
class ExportRequestService
{
    /** Upper bound of invoices in one export; keeps memory and portal load predictable. */
    public const MAX_INVOICES = 3000;

    public function __construct(private readonly TaxRateLimiter $limiter) {}

    /**
     * @param  list<int>|null  $invoiceIds  restrict the export to these cached invoices
     */
    public function request(Company $company, Customer $customer, string $type, InvoiceSearch $search, ?array $invoiceIds = null): TaxExportRun
    {
        if (! in_array($type, [TaxExportRun::TYPE_EXCEL, TaxExportRun::TYPE_XML_ZIP], true)) {
            throw new RuntimeException('Loại xuất file không hợp lệ.');
        }

        if (! $customer->canAccessTenant($company)) {
            throw new RuntimeException('Bạn không có quyền với công ty này.');
        }

        $company->assertLicensed();

        $active = TaxExportRun::withoutGlobalScopes()
            ->where('company_id', $company->getKey())
            ->whereIn('status', [TaxExportRun::STATUS_PENDING, TaxExportRun::STATUS_RUNNING])
            ->where('created_at', '>', now()->subHour())
            ->exists();
        if ($active) {
            throw new RuntimeException('Công ty đang có một lượt xuất file chưa hoàn tất, vui lòng đợi.');
        }

        $this->limiter->hit('export', $company, $customer);

        $filters = $search->toArray() + ['ids' => $invoiceIds ? array_map('intval', $invoiceIds) : null];

        $run = TaxExportRun::withoutGlobalScopes()->create([
            'company_id' => $company->getKey(),
            'customer_id' => $customer->getKey(),
            'type' => $type,
            'filters' => $filters,
            'status' => TaxExportRun::STATUS_PENDING,
        ]);

        RunTaxExport::dispatch($run->getKey());

        return $run;
    }
}
