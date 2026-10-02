<?php

namespace App\Services\Gdt;

use App\Models\Company;
use App\Models\TaxInvoice;
use Illuminate\Support\Carbon;

/**
 * Pulls invoices from the portal into `tax_invoices`. Standard (`query`) and cash-register
 * (`sco-query`) sources are both read, deduplicated by seller/symbol/number, standard winning.
 */
class InvoiceSyncService
{
    private const SORT = 'tdlap:desc';

    private const SOURCES = [
        TaxInvoice::SOURCE_STANDARD => 'query',
        TaxInvoice::SOURCE_MTT => 'sco-query',
    ];

    public function __construct(private readonly GdtSessionService $sessions) {}

    /**
     * @return array{count: int, warnings: list<string>}
     */
    public function sync(Company $company, InvoiceSearch $search): array
    {
        return $this->sessions->run($company, function (GdtClient $client) use ($company, $search) {
            $rows = [];
            $warnings = [];

            foreach (self::SOURCES as $source => $apiBase) {
                try {
                    foreach ($this->fetchAllPages($client, $apiBase, $search) as $row) {
                        $key = ($row['nbmst'] ?? '').'|'.($row['khhdon'] ?? '').'|'.($row['shdon'] ?? '');
                        $rows[$key] ??= [$source, $row];
                    }
                } catch (GdtUnauthorizedException $e) {
                    throw $e;
                } catch (GdtException $e) {
                    // The cash-register source is optional for many taxpayers; the main source is not.
                    if ($source === TaxInvoice::SOURCE_STANDARD) {
                        throw $e;
                    }
                    $warnings[] = 'Không tải được hóa đơn máy tính tiền: '.$e->getMessage();
                }
            }

            foreach ($rows as [$source, $row]) {
                $this->store($company, $search->direction, $source, $row);
            }

            return ['count' => count($rows), 'warnings' => $warnings];
        });
    }

    /**
     * Follow the portal's `state` cursor until a short page signals the end.
     *
     * @return \Generator<int, array<string, mixed>>
     */
    private function fetchAllPages(GdtClient $client, string $apiBase, InvoiceSearch $search): \Generator
    {
        $state = null;

        do {
            $page = $client->invoicePage($apiBase, $search->direction, self::SORT, $search->expression(), $state);
            $items = $page['datas'] ?? $page['data'] ?? [];

            foreach ($items as $item) {
                yield $item;
            }

            $state = (count($items) === GdtClient::PAGE_SIZE && ! empty($page['state'])) ? $page['state'] : null;
        } while ($state);
    }

    private function store(Company $company, string $direction, string $source, array $row): TaxInvoice
    {
        $identity = [
            'company_id' => $company->getKey(),
            'direction' => $direction,
            'symbol' => (string) ($row['khhdon'] ?? ''),
            'number' => (string) ($row['shdon'] ?? ''),
            'mst_seller' => (string) ($row['nbmst'] ?? ''),
        ];

        // `detail` is deliberately absent so a re-sync keeps already fetched line items.
        return TaxInvoice::withoutGlobalScopes()->updateOrCreate($identity, [
            'source' => $source,
            'mst_buyer' => $row['nmmst'] ?? null,
            'seller_name' => $row['nbten'] ?? null,
            'buyer_name' => $row['nmten'] ?? null,
            'template' => isset($row['khmshdon']) ? (string) $row['khmshdon'] : null,
            'issued_at' => ! empty($row['tdlap']) ? Carbon::parse($row['tdlap']) : null,
            'total_before_tax' => $row['tgtcthue'] ?? 0,
            'total_tax' => $row['tgtthue'] ?? 0,
            'total_payment' => $row['tgtttbso'] ?? 0,
            'currency' => $row['dvtte'] ?? 'VND',
            'status' => $row['tthai'] ?? null,
            'check_status' => $row['ttxly'] ?? null,
            'raw' => $row,
        ]);
    }

    /** Fetch and cache the line items of one invoice (no-op when already cached). */
    public function loadDetail(TaxInvoice $invoice, GdtClient $client): TaxInvoice
    {
        if ($invoice->detail) {
            return $invoice;
        }

        $detail = $client->invoiceDetail(
            $invoice->apiBase(),
            $invoice->mst_seller,
            $invoice->symbol,
            $invoice->number,
            (string) $invoice->template,
        );

        $invoice->forceFill(['detail' => $detail])->save();

        return $invoice;
    }
}
