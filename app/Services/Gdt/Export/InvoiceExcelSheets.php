<?php

namespace App\Services\Gdt\Export;

use App\Models\TaxInvoice;
use Illuminate\Support\Collection;

/**
 * Sheet layouts of the Excel export: which columns exist and which row each invoice (or line item)
 * produces. Each sheet is `['name', 'title', 'columns' => [key => [label, width]], 'rows' => list<array>]`.
 */
class InvoiceExcelSheets
{
    private const TTXLY = [1 => 'Chờ xử lý', 2 => 'Đã tiếp nhận', 3 => 'Không hợp lệ', 4 => 'Hủy', 5 => 'Đã cấp mã hóa đơn', 6 => 'Cơ quan thuế từ chối'];

    private const TTHAI = [1 => 'Hóa đơn mới', 2 => 'Hóa đơn thay thế', 3 => 'Hóa đơn điều chỉnh', 4 => 'Đã xóa'];

    private const TCHAT = [1 => 'Hàng hóa, dịch vụ', 2 => 'Khuyến mại', 3 => 'Chiết khấu thương mại', 4 => 'Ghi chú'];

    /** Keys written as text so leading zeros of tax codes and invoice numbers survive. */
    public const TEXT_KEYS = ['khmshdon', 'khhdon', 'shdon', 'nbmst', 'nmmst', 'mahang', 'mauso', 'kyhieu', 'so', 'maTraCuu'];

    /**
     * @param  Collection<int, TaxInvoice>  $invoices
     * @return list<array{name: string, title: string, columns: array<string, array{0: string, 1: int}>, rows: list<array<string, mixed>>}>
     */
    public static function build(Collection $invoices, string $direction): array
    {
        $sold = $direction === TaxInvoice::DIRECTION_SOLD;
        $payloads = $invoices->values()->map(fn (TaxInvoice $i) => ['inv' => $i->payload(), 'type' => self::typeLabel($i, $sold)]);

        $sheets = [
            self::detailSheet($payloads, $sold),
            self::listSheet($payloads, $sold),
            self::productSheet($payloads, $sold),
        ];

        if (! $sold) {
            $sheets[] = self::purchaseLedgerSheet($payloads);
        }

        return $sheets;
    }

    private static function typeLabel(TaxInvoice $invoice, bool $sold): string
    {
        $mtt = $invoice->source === TaxInvoice::SOURCE_MTT;

        return $sold
            ? ($mtt ? 'HĐ MTT (bán ra)' : 'Hóa đơn bán ra')
            : ($mtt ? 'HĐ MTT (mua vào)' : 'Hóa đơn mua vào');
    }

    private static function date(?string $value): string
    {
        return $value ? substr($value, 0, 10) : '';
    }

    /** Invoice-level columns shared by the detail and product sheets. */
    private static function head(array $inv, int $stt, string $type): array
    {
        return [
            'stt' => $stt, 'loaihd' => $type,
            'khmshdon' => $inv['khmshdon'] ?? null, 'khhdon' => $inv['khhdon'] ?? null, 'shdon' => $inv['shdon'] ?? null,
            'tdlap' => self::date($inv['tdlap'] ?? null),
            'nbmst' => $inv['nbmst'] ?? null, 'nbten' => $inv['nbten'] ?? null, 'nbdchi' => $inv['nbdchi'] ?? null,
            'nmmst' => $inv['nmmst'] ?? null, 'nmten' => $inv['nmten'] ?? null, 'nmdchi' => $inv['nmdchi'] ?? null,
            'dvtte' => $inv['dvtte'] ?? 'VND', 'tgia' => $inv['tgia'] ?? 1,
            'tthai' => self::TTHAI[$inv['tthai'] ?? 0] ?? '', 'ttxly' => self::TTXLY[$inv['ttxly'] ?? 0] ?? '',
        ];
    }

    private static function line(?array $p): array
    {
        return [
            'tchat' => $p ? (self::TCHAT[$p['tchat'] ?? 0] ?? '') : '',
            'mahang' => $p['mhang'] ?? null, 'ten_sp' => $p['ten'] ?? null, 'dvtinh' => $p['dvtinh'] ?? null,
            'sluong' => $p['sluong'] ?? null, 'dgia' => $p['dgia'] ?? null, 'tsuat' => $p['ltsuat'] ?? null,
            'thtien' => $p['thtien'] ?? null, 'tthue' => $p ? ($p['tthue'] ?? 0) : null,
        ];
    }

    private static function money(array $inv): array
    {
        return [
            'tgtcthue' => $inv['tgtcthue'] ?? null, 'tgtthue' => $inv['tgtthue'] ?? null,
            'ttcktmai' => $inv['ttcktmai'] ?? 0, 'tgtphi' => $inv['tgtphi'] ?? null, 'tgtttbso' => $inv['tgtttbso'] ?? null,
        ];
    }

    private static function detailSheet(Collection $payloads, bool $sold): array
    {
        $rows = [];
        foreach ($payloads as $i => ['inv' => $inv, 'type' => $type]) {
            foreach (($inv['hdhhdvu'] ?? []) ?: [null] as $p) {
                // Column order is defined by the sheet's `columns`, so merge order does not matter.
                $rows[] = self::head($inv, $i + 1, $type) + self::money($inv) + ['hd_lq' => ''] + self::line($p);
            }
        }

        return [
            'name' => 'Hoa don Chi tiet SP',
            'title' => 'DANH SÁCH CHI TIẾT HÓA ĐƠN '.($sold ? 'BÁN RA' : 'MUA VÀO').' (kèm sản phẩm)',
            'columns' => [
                'stt' => ['STT', 6], 'loaihd' => ['LOẠI HÓA ĐƠN', 18], 'khmshdon' => ['KÝ HIỆU MẪU SỐ', 10],
                'khhdon' => ['KÝ HIỆU HÓA ĐƠN', 12], 'shdon' => ['SỐ HÓA ĐƠN', 12], 'tdlap' => ['NGÀY LẬP', 12],
                'nbmst' => ['MST NGƯỜI BÁN', 14], 'nbten' => ['TÊN NGƯỜI BÁN', 32], 'nbdchi' => ['ĐỊA CHỈ NGƯỜI BÁN', 30],
                'nmmst' => ['MST NGƯỜI MUA', 14], 'nmten' => ['TÊN NGƯỜI MUA', 30], 'nmdchi' => ['ĐỊA CHỈ NGƯỜI MUA', 30],
                'tgtcthue' => ['TỔNG TIỀN CHƯA THUẾ', 18], 'tgtthue' => ['TỔNG TIỀN THUẾ', 14], 'ttcktmai' => ['TỔNG TIỀN CKTM', 14],
                'tgtphi' => ['TỔNG TIỀN PHÍ', 12], 'tgtttbso' => ['TỔNG TIỀN THANH TOÁN', 18], 'dvtte' => ['ĐƠN VỊ TIỀN TỆ', 10],
                'tgia' => ['TỶ GIÁ', 8], 'tthai' => ['TRẠNG THÁI HÓA ĐƠN', 16], 'ttxly' => ['KẾT QUẢ KIỂM TRA', 18],
                'hd_lq' => ['HÓA ĐƠN LIÊN QUAN', 14], 'tchat' => ['TÍNH CHẤT', 16], 'mahang' => ['MÃ HÀNG', 12],
                'ten_sp' => ['TÊN HÀNG HÓA, DỊCH VỤ', 45], 'dvtinh' => ['ĐƠN VỊ TÍNH', 10], 'sluong' => ['SỐ LƯỢNG', 10],
                'dgia' => ['ĐƠN GIÁ', 14], 'tsuat' => ['THUẾ SUẤT', 10], 'thtien' => ['THÀNH TIỀN', 14], 'tthue' => ['TIỀN THUẾ', 12],
            ],
            'rows' => $rows,
        ];
    }

    private static function listSheet(Collection $payloads, bool $sold): array
    {
        $rows = $payloads->map(function (array $item, int $i) {
            $inv = $item['inv'];
            $secret = collect($inv['ttkhac'] ?? [])->firstWhere('ttruong', 'Mã số bí mật');

            return [
                'loai' => $item['type'], 'stt' => $i + 1, 'khhdon' => $inv['khhdon'] ?? null, 'shdon' => $inv['shdon'] ?? null,
                'tdlap' => self::date($inv['tdlap'] ?? null), 'nky' => self::date($inv['nky'] ?? null),
                'nbmst' => $inv['nbmst'] ?? null, 'nbten' => $inv['nbten'] ?? null, 'nbdchi' => $inv['nbdchi'] ?? null,
                'nmmst' => $inv['nmmst'] ?? null, 'nmten' => $inv['nmten'] ?? null, 'nmdchi' => $inv['nmdchi'] ?? null,
            ] + self::money($inv) + [
                'dvtte' => $inv['dvtte'] ?? 'VND', 'tgia' => $inv['tgia'] ?? 1,
                'tthai' => self::TTHAI[$inv['tthai'] ?? 0] ?? '', 'ttxly' => self::TTXLY[$inv['ttxly'] ?? 0] ?? '',
                'thtttoan' => $inv['thtttoan'] ?? null, 'maTraCuu' => $secret['dlieu'] ?? '', 'coXml' => '✓',
            ];
        })->all();

        return [
            'name' => 'DS hoa don',
            'title' => 'DANH SÁCH HÓA ĐƠN '.($sold ? 'BÁN RA' : 'MUA VÀO'),
            'columns' => [
                'loai' => ['LOẠI HÓA ĐƠN', 14], 'stt' => ['STT', 6], 'khhdon' => ['KÝ HIỆU', 12], 'shdon' => ['SỐ', 12],
                'tdlap' => ['NGÀY LẬP', 12], 'nky' => ['NGÀY KÝ', 12], 'nbmst' => ['MST NGƯỜI BÁN', 14], 'nbten' => ['TÊN NGƯỜI BÁN', 32],
                'nbdchi' => ['ĐỊA CHỈ NGƯỜI BÁN', 28], 'nmmst' => ['MST NGƯỜI MUA', 14], 'nmten' => ['TÊN NGƯỜI MUA', 30],
                'nmdchi' => ['ĐỊA CHỈ NGƯỜI MUA', 28], 'tgtcthue' => ['TỔNG TIỀN CHƯA THUẾ', 18], 'tgtthue' => ['TỔNG TIỀN THUẾ', 14],
                'ttcktmai' => ['TỔNG CKTM', 12], 'tgtphi' => ['TỔNG PHÍ', 10], 'tgtttbso' => ['TỔNG THANH TOÁN', 16],
                'dvtte' => ['TIỀN TỆ', 8], 'tgia' => ['TỶ GIÁ', 8], 'tthai' => ['TRẠNG THÁI', 14], 'ttxly' => ['KQ KIỂM TRA', 18],
                'thtttoan' => ['HÌNH THỨC THANH TOÁN', 16], 'maTraCuu' => ['MÃ TRA CỨU', 16], 'coXml' => ['CÓ XML', 8],
            ],
            'rows' => $rows,
        ];
    }

    private static function productSheet(Collection $payloads, bool $sold): array
    {
        $rows = [];
        foreach ($payloads as $i => ['inv' => $inv, 'type' => $type]) {
            foreach ($inv['hdhhdvu'] ?? [] as $p) {
                $rows[] = self::head($inv, $i + 1, $type) + self::line($p) + ['loaihh' => ''];
            }
        }

        return [
            'name' => 'DS san pham',
            'title' => 'DANH SÁCH SẢN PHẨM ('.($sold ? 'bán ra' : 'mua vào').')',
            'columns' => [
                'stt' => ['STT', 6], 'loaihd' => ['LOẠI HÓA ĐƠN', 18], 'khmshdon' => ['KÝ HIỆU MẪU SỐ', 10], 'khhdon' => ['KÝ HIỆU HÓA ĐƠN', 12],
                'shdon' => ['SỐ HÓA ĐƠN', 12], 'tdlap' => ['NGÀY LẬP', 12], 'nbmst' => ['MST NGƯỜI BÁN', 14], 'nbten' => ['TÊN NGƯỜI BÁN', 32],
                'nbdchi' => ['ĐỊA CHỈ NGƯỜI BÁN', 28], 'nmmst' => ['MST NGƯỜI MUA', 14], 'nmten' => ['TÊN NGƯỜI MUA', 30],
                'nmdchi' => ['ĐỊA CHỈ NGƯỜI MUA', 28], 'dvtte' => ['ĐƠN VỊ TIỀN TỆ', 10], 'tgia' => ['TỶ GIÁ', 8],
                'tthai' => ['TRẠNG THÁI HÓA ĐƠN', 16], 'ttxly' => ['KẾT QUẢ KIỂM TRA', 18], 'tchat' => ['TÍNH CHẤT', 16],
                'mahang' => ['MÃ HÀNG', 12], 'loaihh' => ['LOẠI HÀNG HÓA ĐẶC TRƯNG', 18], 'ten_sp' => ['TÊN HÀNG HÓA, DỊCH VỤ', 45],
                'dvtinh' => ['ĐƠN VỊ TÍNH', 10], 'sluong' => ['SỐ LƯỢNG', 10], 'dgia' => ['ĐƠN GIÁ', 14], 'tsuat' => ['THUẾ SUẤT', 10],
                'thtien' => ['THÀNH TIỀN', 14], 'tthue' => ['TIỀN THUẾ', 12],
            ],
            'rows' => $rows,
        ];
    }

    /** Purchase ledger layout of Circular 80 (bảng kê mua vào). */
    private static function purchaseLedgerSheet(Collection $payloads): array
    {
        $rows = [];
        foreach ($payloads as $i => ['inv' => $inv]) {
            foreach ($inv['hdhhdvu'] ?? [] as $p) {
                $rows[] = [
                    'stt' => $i + 1, 'mauso' => $inv['khmshdon'] ?? null, 'kyhieu' => $inv['khhdon'] ?? null, 'so' => $inv['shdon'] ?? null,
                    'ngay' => self::date($inv['tdlap'] ?? null), 'nbten' => $inv['nbten'] ?? null, 'nbmst' => $inv['nbmst'] ?? null,
                    'ten_sp' => $p['ten'] ?? null, 'dvtinh' => $p['dvtinh'] ?? null, 'sluong' => $p['sluong'] ?? null,
                    'dgia' => $p['dgia'] ?? null, 'thtien' => $p['thtien'] ?? null, 'tsuat' => $p['ltsuat'] ?? null,
                    'tthue' => $p['tthue'] ?? 0, 'ghichu' => '',
                ];
            }
        }

        return [
            'name' => 'BK mua vao TT80',
            'title' => 'BẢNG KÊ HOÁ ĐƠN, CHỨNG TỪ HÀNG HOÁ, DỊCH VỤ MUA VÀO',
            'columns' => [
                'stt' => ['STT', 6], 'mauso' => ['Mẫu số', 8], 'kyhieu' => ['Ký hiệu', 10], 'so' => ['Số', 10],
                'ngay' => ['Ngày, tháng, năm', 14], 'nbten' => ['Tên người bán', 32], 'nbmst' => ['Mã số thuế người bán', 16],
                'ten_sp' => ['Tên hàng hóa, dịch vụ', 45], 'dvtinh' => ['Đơn vị tính', 10], 'sluong' => ['Số lượng', 10],
                'dgia' => ['Đơn giá', 14], 'thtien' => ['Giá trị HHDV mua vào chưa có thuế GTGT', 20],
                'tsuat' => ['Thuế suất (%)', 10], 'tthue' => ['Tiền thuế GTGT', 14], 'ghichu' => ['Ghi chú', 12],
            ],
            'rows' => $rows,
        ];
    }
}
