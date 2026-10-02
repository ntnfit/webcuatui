<?php

namespace App\Services\Gdt\Export;

use App\Models\TaxInvoice;
use App\Services\Gdt\GdtClient;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Throwable;

/**
 * Printable HTML view of an invoice. Uses the portal's own rendering from the export archive when
 * present, otherwise a plain layout built from the invoice data. Scripts are always stripped, so the
 * result is inert markup safe to show in a sandboxed frame; PDF is produced by the browser's print dialog.
 */
class InvoiceHtmlRenderer
{
    private const IMAGE_MIME = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif',
        'svg' => 'image/svg+xml', 'bmp' => 'image/bmp', 'webp' => 'image/webp'];

    public function __construct(private readonly InvoiceXmlExporter $xml) {}

    public function render(TaxInvoice $invoice, GdtClient $client): string
    {
        $entries = InvoiceArchive::entries($this->xml->fetchArchive($invoice, $client));
        $html = $entries ? $this->fromArchive($entries, $invoice) : null;

        return $this->finish($html ?? $this->fallback($invoice->payload()));
    }

    /** @param array<string, string> $entries */
    private function fromArchive(array $entries, TaxInvoice $invoice): ?string
    {
        $htmlName = collect(array_keys($entries))->first(fn (string $n) => preg_match('/\.html?$/i', $n));
        if ($htmlName === null) {
            return null;
        }
        $html = $entries[$htmlName];

        foreach ($entries as $name => $bytes) {
            $mime = self::IMAGE_MIME[strtolower(pathinfo($name, PATHINFO_EXTENSION))] ?? null;
            if ($mime === null) {
                continue;
            }
            $uri = 'data:'.$mime.';base64,'.base64_encode($bytes);
            // Match by base name so references with any path prefix resolve to the embedded copy.
            $html = preg_replace('~url\([\'"]?[^)]*'.preg_quote(basename($name), '~').'[^)]*[\'"]?\)~', 'url('.$uri.')', $html);
            $html = str_replace([$name, basename($name)], $uri, $html);
        }

        $qr = preg_match('/id=["\']qrcodeContent["\'][^>]*value=["\']([^"\']*)["\']/', $html, $m) ? $m[1] : ($invoice->payload()['qrcode'] ?? null);
        if ($qr && ($svg = $this->qrDataUri($qr))) {
            $html = preg_replace(
                '~(<div[^>]*id=["\']qrcodeTable["\'][^>]*>)(.*?)(</div>)~s',
                '$1<img src="'.$svg.'" style="width:80px;height:80px" alt="QR"/>$3',
                $html,
                1,
            );
        }

        // Show only the common name of the signer.
        if (preg_match('/id=["\']cks["\'][^>]*>([^<]+)</', $html, $m) && preg_match('/CN=([^,]+)/', $m[1], $cn)) {
            $html = preg_replace('/(<[^>]*id=["\']cks["\'][^>]*>)[^<]*/', '${1}'.addcslashes(e($cn[1]), '\\$'), $html, 1);
        }

        return $html;
    }

    private function finish(string $html): string
    {
        $html = preg_replace('~<script\b[^>]*>.*?</script>~is', '', $html);
        $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);

        $style = '<style>'.(str_contains($html, '@page') ? '' : '@page{size:A4;margin:0}body{font-family:"Times New Roman",serif}')
            .'*{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}</style>';

        return str_contains($html, '</head>') ? str_replace('</head>', $style.'</head>', $html) : $style.$html;
    }

    private function qrDataUri(string $data): ?string
    {
        if (! class_exists(Writer::class)) {
            return null;
        }

        try {
            $svg = (new Writer(new ImageRenderer(new RendererStyle(140, 1), new SvgImageBackEnd)))->writeString($data);

            return 'data:image/svg+xml;base64,'.base64_encode($svg);
        } catch (Throwable) {
            return null;
        }
    }

    /** Plain layout from invoice data, used when the portal archive carries no HTML. */
    private function fallback(array $inv): string
    {
        $money = fn ($v) => $v === null ? '' : number_format((float) $v, 0, ',', '.');
        $date = fn ($d) => $d ? implode('/', array_reverse(explode('-', substr($d, 0, 10)))) : '';
        $rows = '';

        foreach (($inv['hdhhdvu'] ?? []) as $i => $p) {
            $rows .= '<tr><td class="c">'.($i + 1).'</td><td>'.e($p['ten'] ?? '').'</td><td class="c">'.e($p['dvtinh'] ?? '').'</td>'
                .'<td class="r">'.e($p['sluong'] ?? '').'</td><td class="r">'.$money($p['dgia'] ?? null).'</td>'
                .'<td class="r">'.$money($p['thtien'] ?? null).'</td><td class="c">'.e($p['ltsuat'] ?? '').'</td>'
                .'<td class="r">'.$money($p['tthue'] ?? null).'</td></tr>';
        }

        $party = fn (string $label, string $p) => '<div class="box"><h3>'.$label.'</h3><p><b>'.e($inv[$p.'ten'] ?? '').'</b></p>'
            .'<p>MST: '.e($inv[$p.'mst'] ?? '').'</p><p>Địa chỉ: '.e($inv[$p.'dchi'] ?? '').'</p></div>';
        $qr = ! empty($inv['qrcode']) && ($uri = $this->qrDataUri($inv['qrcode'])) ? '<img class="qr" src="'.$uri.'" alt="QR"/>' : '';

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>'
            .'*{box-sizing:border-box}body{font:11pt "Times New Roman",serif;color:#000;margin:0}.wrap{max-width:210mm;margin:0 auto;padding:12mm 15mm}'
            .'h2{text-align:center;color:#c00;text-transform:uppercase;margin:0 0 2mm}.sub{text-align:center;font-size:10pt}'
            .'.parties{display:flex;gap:10mm;margin:6mm 0}.box{flex:1}.box h3{font-size:10pt;border-bottom:1px solid #999}.box p{margin:1mm 0;font-size:10pt}'
            .'table{width:100%;border-collapse:collapse;font-size:10pt}th{background:#333;color:#fff;padding:2mm}td{border:.5px solid #999;padding:1.5mm 3mm}'
            .'.c{text-align:center}.r{text-align:right}.tot{width:50%;margin:4mm 0 0 auto}.tot td{border:0}.qr{width:140px;float:right}'
            .'</style></head><body><div class="wrap">'
            .'<h2>Hóa đơn giá trị gia tăng</h2><div class="sub">Ký hiệu: <b>'.e($inv['khhdon'] ?? '').'</b> Số: <b>'.e($inv['shdon'] ?? '').'</b> Mẫu số: <b>'.e($inv['khmshdon'] ?? '').'</b></div>'
            .'<div class="sub">Ngày lập: <b>'.$date($inv['tdlap'] ?? null).'</b></div>'.$qr
            .'<div class="parties">'.$party('Người bán', 'nb').$party('Người mua', 'nm').'</div>'
            .'<table><thead><tr><th>STT</th><th>Tên hàng hóa, dịch vụ</th><th>ĐVT</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th><th>Thuế suất</th><th>Tiền thuế</th></tr></thead><tbody>'.$rows.'</tbody></table>'
            .'<table class="tot"><tr><td class="r">Tổng tiền chưa thuế:</td><td class="r"><b>'.$money($inv['tgtcthue'] ?? null).'</b></td></tr>'
            .'<tr><td class="r">Tổng tiền thuế GTGT:</td><td class="r"><b>'.$money($inv['tgtthue'] ?? null).'</b></td></tr>'
            .'<tr><td class="r">Tổng thanh toán:</td><td class="r"><b>'.$money($inv['tgtttbso'] ?? null).' '.e($inv['dvtte'] ?? 'VND').'</b></td></tr></table>'
            .'</div></body></html>';
    }
}
