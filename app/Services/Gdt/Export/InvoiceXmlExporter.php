<?php

namespace App\Services\Gdt\Export;

use App\Models\TaxInvoice;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\GdtUnauthorizedException;
use RuntimeException;
use ZipArchive;

/** Downloads invoice XML from the portal, singly or bundled into a zip. */
class InvoiceXmlExporter
{
    /** File name prefix shared by every artifact of one invoice. */
    public static function prefix(TaxInvoice $invoice): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', "{$invoice->mst_seller}_{$invoice->symbol}_{$invoice->number}");
    }

    /**
     * Raw portal archive for an invoice (zip holding the XML and the HTML view).
     */
    public function fetchArchive(TaxInvoice $invoice, GdtClient $client): string
    {
        return $client->exportXml(
            $invoice->apiBase(),
            $invoice->mst_seller,
            $invoice->symbol,
            $invoice->number,
            (string) $invoice->template,
        );
    }

    /**
     * One invoice as a downloadable file: the bare XML when the archive has exactly one,
     * otherwise the archive bytes as returned by the portal.
     *
     * @return array{name: string, content: string, mime: string}
     */
    public function single(TaxInvoice $invoice, GdtClient $client): array
    {
        $bytes = $this->fetchArchive($invoice, $client);
        $entries = InvoiceArchive::entries($bytes);
        $prefix = self::prefix($invoice);

        if ($entries === null) {
            return ['name' => "{$prefix}.xml", 'content' => $bytes, 'mime' => 'application/xml'];
        }

        $xml = array_filter($entries, fn (string $name) => str_ends_with(strtolower($name), '.xml'), ARRAY_FILTER_USE_KEY);

        if (count($xml) === 1) {
            return ['name' => "{$prefix}.xml", 'content' => reset($xml), 'mime' => 'application/xml'];
        }

        return ['name' => "{$prefix}.zip", 'content' => $bytes, 'mime' => 'application/zip'];
    }

    /**
     * Bundle XML of many invoices into one zip at $path. A single invoice that fails is skipped
     * and reported; an expired token aborts the whole run.
     *
     * @param  iterable<TaxInvoice>  $invoices
     * @return array{added: int, failed: list<string>}
     */
    public function buildZip(iterable $invoices, GdtClient $client, string $path, ?callable $progress = null): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Không tạo được file zip.');
        }

        $added = 0;
        $failed = [];

        foreach ($invoices as $invoice) {
            $prefix = self::prefix($invoice);
            try {
                $bytes = $this->fetchArchive($invoice, $client);
            } catch (GdtUnauthorizedException $e) {
                $zip->close();
                throw $e;
            } catch (GdtException) {
                $failed[] = "{$invoice->symbol}-{$invoice->number}";
                $progress && $progress();

                continue;
            }

            $entries = InvoiceArchive::entries($bytes);
            if ($entries === null) {
                $zip->addFromString("{$prefix}.xml", $bytes);
            } else {
                foreach ($entries as $name => $content) {
                    $zip->addFromString($prefix.'_'.basename($name), $content);
                }
            }

            $added++;
            $progress && $progress();
        }

        $zip->close();

        return ['added' => $added, 'failed' => $failed];
    }
}
