<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\GdtSession;
use App\Models\TaxExportRun;
use App\Models\TaxInvoice;
use App\Services\Gdt\Export\ExportRequestService;
use App\Services\Gdt\Export\InvoiceHtmlRenderer;
use App\Services\Gdt\Export\InvoiceXmlExporter;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\InvoiceSearch;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

beforeEach(function () {
    GdtClient::$initialDelayMs = 0;
    Storage::fake('local');
    $this->company = Company::factory()->create();
    $this->customer = $this->company->owner;
    GdtSession::withoutGlobalScopes()->create([
        'company_id' => $this->company->id, 'token' => 'tok', 'expires_at' => now()->addHour(),
    ]);
});

function exportSearch(string $direction = 'purchase'): InvoiceSearch
{
    return new InvoiceSearch($direction, Carbon::now()->subDays(25), Carbon::now());
}

function zipBytes(array $files): string
{
    $tmp = tempnam(sys_get_temp_dir(), 'z');
    $zip = new ZipArchive;
    $zip->open($tmp, ZipArchive::OVERWRITE);
    foreach ($files as $name => $content) {
        $zip->addFromString($name, $content);
    }
    $zip->close();
    $bytes = file_get_contents($tmp);
    unlink($tmp);

    return $bytes;
}

function seedInvoices(Company $company, int $n, array $attrs = []): void
{
    TaxInvoice::factory()->count($n)->create($attrs + ['company_id' => $company->id, 'issued_at' => now()->subDays(2)]);
}

it('builds an excel workbook in the background, fetching missing line items, and notifies the customer', function () {
    seedInvoices($this->company, 2);
    Http::fake(['*/invoices/detail*' => Http::response([
        'khhdon' => 'C26TAA', 'tgtttbso' => 1100,
        'hdhhdvu' => [['ten' => '=HYPERLINK("http://evil")', 'sluong' => 2, 'dgia' => 500, 'thtien' => 1000, 'ltsuat' => '10%', 'tthue' => 100, 'tchat' => 1]],
    ])]);

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_EXCEL, exportSearch());

    $run = $run->fresh();
    expect($run->status)->toBe('done')->and($run->file_path)->not->toBeNull();
    Storage::disk('local')->assertExists($run->file_path);

    $book = IOFactory::load(Storage::disk('local')->path($run->file_path));
    expect($book->getSheetNames())->toBe(['Hoa don Chi tiet SP', 'DS hoa don', 'DS san pham', 'BK mua vao TT80']);
    $cell = $book->getSheetByName('DS san pham')->getCell('T5');
    expect($cell->getDataType())->toBe('s')->and($cell->getValue())->toStartWith('=HYPERLINK');
    expect($book->getSheetByName('DS hoa don')->getHighestRow())->toBe(6);

    expect($this->customer->notifications()->count())->toBe(1);
});

it('omits the purchase ledger sheet for sold invoices', function () {
    seedInvoices($this->company, 1, ['direction' => 'sold']);
    Http::fake(['*/invoices/detail*' => Http::response(['hdhhdvu' => []])]);

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_EXCEL, exportSearch('sold'));

    $book = IOFactory::load(Storage::disk('local')->path($run->fresh()->file_path));
    expect($book->getSheetNames())->not->toContain('BK mua vao TT80');
});

it('zips the xml of each invoice, unpacking portal archives', function () {
    seedInvoices($this->company, 2);
    Http::fake(['*/invoices/export-xml*' => Http::response(zipBytes(['invoice.xml' => '<HDon/>', 'invoice.html' => '<html/>']))]);

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_XML_ZIP, exportSearch());

    $run = $run->fresh();
    expect($run->status)->toBe('done');
    $zip = new ZipArchive;
    $zip->open(Storage::disk('local')->path($run->file_path));
    expect($zip->numFiles)->toBe(4);
    $name = $zip->getNameIndex(0);
    expect($name)->toMatch('/^\d+_C26TAA_\d+_invoice\.(xml|html)$/');
});

it('reports a partial xml failure without aborting the run', function () {
    seedInvoices($this->company, 2);
    Http::fakeSequence('*/invoices/export-xml*')->push('<HDon/>')->push([], 500);

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_XML_ZIP, exportSearch());

    $run = $run->fresh();
    expect($run->status)->toBe('done')->and($run->error)->toContain('1 hóa đơn không tải được XML');
});

it('fails the run and notifies when the token has expired', function () {
    seedInvoices($this->company, 1);
    GdtSession::withoutGlobalScopes()->delete();

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_XML_ZIP, exportSearch());

    expect($run->fresh()->status)->toBe('failed')->and($run->fresh()->error)->toContain('kết nối');
    expect($this->customer->notifications()->first()->data['status'] ?? null)->toBe('danger');
});

it('fails with a helpful message when there is nothing to export', function () {
    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_EXCEL, exportSearch());

    expect($run->fresh()->status)->toBe('failed')->and($run->fresh()->error)->toContain('Không có hóa đơn');
});

it('never exports invoices of another company', function () {
    $other = Company::factory()->create();
    seedInvoices($this->company, 1);
    seedInvoices($other, 3);
    Http::fake(['*/invoices/detail*' => Http::response(['hdhhdvu' => []])]);

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_EXCEL, exportSearch());

    $book = IOFactory::load(Storage::disk('local')->path($run->fresh()->file_path));
    expect($book->getSheetByName('DS hoa don')->getHighestRow())->toBe(5); // header at 4, one invoice
});

it('refuses selected ids that belong to another company', function () {
    $other = Company::factory()->create();
    seedInvoices($other, 1);
    $foreignId = TaxInvoice::withoutGlobalScopes()->where('company_id', $other->id)->value('id');

    $run = app(ExportRequestService::class)->request($this->company, $this->customer, TaxExportRun::TYPE_XML_ZIP, exportSearch(), [$foreignId]);

    expect($run->fresh()->status)->toBe('failed');
});

it('rejects exports requested by a non-member', function () {
    $stranger = Customer::factory()->create();

    expect(fn () => app(ExportRequestService::class)->request($this->company, $stranger, TaxExportRun::TYPE_EXCEL, exportSearch()))
        ->toThrow(RuntimeException::class, 'quyền');
    expect(TaxExportRun::withoutGlobalScopes()->count())->toBe(0);
});

it('blocks a second export while one is active and rate limits repeated exports', function () {
    TaxExportRun::withoutGlobalScopes()->create([
        'company_id' => $this->company->id, 'type' => 'excel', 'filters' => [], 'status' => 'running',
    ]);

    expect(fn () => app(ExportRequestService::class)->request($this->company, $this->customer, 'excel', exportSearch()))
        ->toThrow(RuntimeException::class, 'chưa hoàn tất');

    TaxExportRun::withoutGlobalScopes()->delete();
    $service = app(ExportRequestService::class);
    for ($i = 0; $i < 5; $i++) {
        $service->request($this->company, $this->customer, 'excel', exportSearch());
    }

    expect(fn () => $service->request($this->company, $this->customer, 'excel', exportSearch()))
        ->toThrow(RuntimeException::class, 'quá nhanh');
});

it('lets only company members read an export run', function () {
    $run = TaxExportRun::withoutGlobalScopes()->create([
        'company_id' => $this->company->id, 'type' => 'excel', 'filters' => [], 'status' => 'done',
    ]);
    $stranger = Customer::factory()->create();

    expect($this->customer->can('view', $run))->toBeTrue()
        ->and($stranger->can('view', $run))->toBeFalse()
        ->and($stranger->can('delete', $run))->toBeFalse();
});

it('returns a single invoice xml, unwrapping the portal archive', function () {
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);
    Http::fake(['*/invoices/export-xml*' => Http::response(zipBytes(['a.xml' => '<HDon/>', 'a.html' => '<html/>']))]);

    $file = app(InvoiceXmlExporter::class)->single($invoice, new GdtClient('tok'));

    expect($file['content'])->toBe('<HDon/>')->and($file['name'])->toEndWith('.xml');
});

it('renders the portal html inline with images embedded and scripts removed', function () {
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);
    $html = '<html><head><style>.bg{background:url(/static/bg.png)}</style><script>alert(1)</script></head>'
        .'<body onload="evil()"><img src="logo.png"/>Hoa don</body></html>';
    Http::fake(['*/invoices/export-xml*' => Http::response(zipBytes(['inv.html' => $html, 'logo.png' => 'PNGDATA', 'bg.png' => 'BG']))]);

    $out = app(InvoiceHtmlRenderer::class)->render($invoice, new GdtClient('tok'));

    expect($out)->not->toContain('<script')->not->toContain('onload')->not->toContain('logo.png"')
        ->and($out)->toContain('data:image/png;base64,'.base64_encode('PNGDATA'))
        ->and($out)->toContain('@page');
});

it('falls back to a generated layout and escapes invoice text', function () {
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id, 'detail' => [
        'khhdon' => 'C26TAA', 'shdon' => '9', 'nbten' => '<script>x</script>Cong ty', 'hdhhdvu' => [['ten' => 'May tinh', 'thtien' => 1000]],
    ]]);
    Http::fake(['*/invoices/export-xml*' => Http::response('not a zip')]);

    $out = app(InvoiceHtmlRenderer::class)->render($invoice, new GdtClient('tok'));

    expect($out)->toContain('May tinh')->toContain('&lt;script&gt;')->not->toContain('<script>x');
});

it('sends the export request with the invoice identifiers and company token only', function () {
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id, 'source' => 'mtt']);
    Http::fake(['*' => Http::response('<HDon/>')]);

    app(InvoiceXmlExporter::class)->single($invoice, new GdtClient('tok'));

    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/api/sco-query/invoices/export-xml')
        && str_contains($r->url(), 'shdon='.$invoice->number));
});
