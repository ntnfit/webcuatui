<?php

namespace App\Services\Gdt\Export;

use ZipArchive;

/** Reads the zip archives the portal returns from `export-xml`, entirely in memory from the caller's view. */
final class InvoiceArchive
{
    /**
     * @return array<string, string>|null entry name => bytes, or null when $bytes is not a zip archive
     */
    public static function entries(string $bytes): ?array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'gdt');
        if ($tmp === false) {
            return null;
        }

        try {
            file_put_contents($tmp, $bytes);
            $zip = new ZipArchive;

            if ($zip->open($tmp) !== true) {
                return null;
            }

            $entries = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                // Skip directories and anything that could escape the archive on extraction.
                if ($name === false || str_ends_with($name, '/') || str_contains($name, '..')) {
                    continue;
                }
                $content = $zip->getFromIndex($i);
                if ($content !== false) {
                    $entries[$name] = $content;
                }
            }
            $zip->close();

            return $entries;
        } finally {
            @unlink($tmp);
        }
    }
}
