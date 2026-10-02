<?php

namespace App\Services\Gdt\Export;

use App\Models\TaxInvoice;
use App\Services\Gdt\InvoiceSearch;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** Writes the multi-sheet invoice workbook. Invoices should already carry their line items (`detail`). */
class InvoiceExcelExporter
{
    private const HEADER_ROW = 4;

    /**
     * @param  Collection<int, TaxInvoice>  $invoices
     */
    public function write(Collection $invoices, InvoiceSearch $search, string $path): void
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        foreach (InvoiceExcelSheets::build($invoices, $search->direction) as $sheet) {
            $this->addSheet($spreadsheet, $sheet, $search->label());
        }

        $spreadsheet->setActiveSheetIndex(0);
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @param  array{name: string, title: string, columns: array<string, array{0: string, 1: int}>, rows: list<array<string, mixed>>}  $def
     */
    private function addSheet(Spreadsheet $spreadsheet, array $def, string $periodLabel): void
    {
        $ws = $spreadsheet->createSheet();
        $ws->setTitle($def['name']);

        $keys = array_keys($def['columns']);
        $lastCol = Coordinate::stringFromColumnIndex(count($keys));

        $ws->mergeCells("A1:{$lastCol}1");
        $ws->setCellValue('A1', $def['title']);
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $ws->getRowDimension(1)->setRowHeight(30);

        $ws->mergeCells("A2:{$lastCol}2");
        $ws->setCellValue('A2', $periodLabel);
        $ws->getStyle('A2')->getFont()->setItalic(true);
        $ws->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($keys as $index => $key) {
            $col = Coordinate::stringFromColumnIndex($index + 1);
            $ws->setCellValue($col.self::HEADER_ROW, $def['columns'][$key][0]);
            $ws->getColumnDimension($col)->setWidth($def['columns'][$key][1]);
        }
        $this->styleHeader($ws, 'A'.self::HEADER_ROW.":{$lastCol}".self::HEADER_ROW);

        $row = self::HEADER_ROW + 1;
        foreach ($def['rows'] as $data) {
            foreach ($keys as $index => $key) {
                $this->setValue($ws, Coordinate::stringFromColumnIndex($index + 1).$row, $key, $data[$key] ?? null);
            }
            $row++;
        }
    }

    private function setValue(Worksheet $ws, string $cell, string $key, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (in_array($key, InvoiceExcelSheets::TEXT_KEYS, true) || ! is_numeric($value)) {
            // Explicit strings also stop formula injection from portal-supplied text such as "=1+1".
            $ws->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);

            return;
        }

        $ws->setCellValueExplicit($cell, $value + 0, DataType::TYPE_NUMERIC);
    }

    private function styleHeader(Worksheet $ws, string $range): void
    {
        $style = $ws->getStyle($range);
        $style->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E79');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $ws->getRowDimension(self::HEADER_ROW)->setRowHeight(32);
    }
}
