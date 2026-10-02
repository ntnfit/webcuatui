<?php

use App\Filament\Customer\Pages\ConnectGdt;
use App\Filament\Customer\Resources\TaxExportRuns\Pages\ListTaxExportRuns;
use App\Filament\Customer\Resources\TaxInvoices\Pages\ListTaxInvoices;
use App\Models\Company;
use App\Models\GdtSession;
use App\Models\TaxExportRun;
use App\Models\TaxInvoice;
use App\Services\Gdt\GdtClient;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
    GdtClient::$initialDelayMs = 0;
    $this->company = Company::factory()->licensed()->create();
    $this->other = Company::factory()->create();
    $this->actingAs($this->company->owner, 'customer');
    Filament::setCurrentPanel('customer');
    Filament::setTenant($this->company);
});

function jwtFor(int $exp): string
{
    $b64 = fn (array $d) => rtrim(strtr(base64_encode(json_encode($d)), '+/', '-_'), '=');

    return $b64(['alg' => 'none']).'.'.$b64(['exp' => $exp]).'.s';
}

/** Rendered modal body of the currently mounted action (not part of the Livewire test HTML). */
function modalHtml($page): string
{
    return (string) $page->instance()->getMountedAction()->getModalContent();
}

it('shows the captcha and connects without keeping the password in component state', function () {
    Http::fake([
        '*/api/captcha' => Http::response(['key' => 'k1', 'content' => '<svg xmlns="http://www.w3.org/2000/svg"/>']),
        '*/authenticate' => Http::response(['token' => jwtFor(now()->addHour()->getTimestamp())]),
    ]);

    $page = Livewire::test(ConnectGdt::class)
        ->assertSee('data:image/svg+xml;base64,')
        ->fillForm(['password' => 'S3cret!', 'captcha' => 'abcd'])
        ->call('connect')
        ->assertHasNoFormErrors();

    expect($page->get('data'))->not->toHaveKey('password');
    expect(GdtSession::withoutGlobalScopes()->where('company_id', $this->company->id)->exists())->toBeTrue();
    Http::assertSent(fn ($r) => str_contains($r->url(), 'authenticate') && $r['ckey'] === 'k1' && $r['cvalue'] === 'abcd');
});

it('keeps the company disconnected when the portal rejects the login', function () {
    Http::fake([
        '*/api/captcha' => Http::response(['key' => 'k1', 'content' => '<svg/>']),
        '*/authenticate' => Http::response(['message' => 'Sai mã captcha'], 400),
    ]);

    Livewire::test(ConnectGdt::class)
        ->fillForm(['password' => 'x', 'captcha' => 'bad'])
        ->call('connect')
        ->assertNotified('Kết nối thất bại');

    expect(GdtSession::withoutGlobalScopes()->count())->toBe(0);
});

it('lists only the active company invoices', function () {
    $mine = TaxInvoice::factory()->count(2)->create(['company_id' => $this->company->id]);
    $theirs = TaxInvoice::factory()->count(2)->create(['company_id' => $this->other->id]);

    Livewire::test(ListTaxInvoices::class)
        ->assertCanSeeTableRecords($mine)
        ->assertCanNotSeeTableRecords($theirs);
});

it('loads invoices from the portal through the header action', function () {
    GdtSession::withoutGlobalScopes()->create(['company_id' => $this->company->id, 'token' => 't', 'expires_at' => now()->addHour()]);
    Http::fake([
        '*/api/query/invoices/purchase*' => Http::response(['datas' => [[
            'nbmst' => '0300000001', 'khhdon' => 'C26TAA', 'shdon' => '5', 'khmshdon' => 1, 'tdlap' => '2026-09-10T00:00:00',
            'tgtcthue' => 100, 'tgtthue' => 10, 'tgtttbso' => 110,
        ]]]),
        '*/api/sco-query/*' => Http::response(['datas' => []]),
    ]);

    Livewire::test(ListTaxInvoices::class)
        ->callAction('sync', ['direction' => 'purchase', 'from' => '2026-09-01', 'to' => '2026-09-30'])
        ->assertNotified();

    expect(TaxInvoice::withoutGlobalScopes()->where('company_id', $this->company->id)->count())->toBe(1);
    expect(TaxInvoice::withoutGlobalScopes()->where('company_id', $this->other->id)->count())->toBe(0);
});

it('rejects a lookup period over 30 days', function () {
    Livewire::test(ListTaxInvoices::class)
        ->callAction('sync', ['direction' => 'purchase', 'from' => '2026-01-01', 'to' => '2026-03-31'])
        ->assertNotified('Không thể tải hóa đơn');
});

it('queues an excel export from the list page', function () {
    TaxInvoice::factory()->create(['company_id' => $this->company->id, 'issued_at' => now()->subDay(), 'detail' => ['hdhhdvu' => []]]);
    Storage::fake('local');

    Livewire::test(ListTaxInvoices::class)
        ->callAction('export_excel', [
            'direction' => 'purchase', 'from' => now()->subDays(5)->toDateString(), 'to' => now()->toDateString(),
        ])
        ->assertNotified('Đã đưa vào hàng đợi');

    expect(TaxExportRun::withoutGlobalScopes()->where('company_id', $this->company->id)->value('status'))->toBe('done');
});

it('lists only the active company export runs', function () {
    $mine = TaxExportRun::withoutGlobalScopes()->create(['company_id' => $this->company->id, 'type' => 'excel', 'status' => 'done', 'filters' => ['direction' => 'sold', 'from' => 'a', 'to' => 'b']]);
    $theirs = TaxExportRun::withoutGlobalScopes()->create(['company_id' => $this->other->id, 'type' => 'excel', 'status' => 'done', 'filters' => ['direction' => 'sold', 'from' => 'a', 'to' => 'b']]);

    Livewire::test(ListTaxExportRuns::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

it('hides another company pages behind a 404', function () {
    $this->get("/customer/{$this->other->id}/tax-invoices")->assertNotFound();
    $this->get("/customer/{$this->other->id}/tax-export-runs")->assertNotFound();
    $this->get("/customer/{$this->other->id}/connect-gdt")->assertNotFound();
    $this->get("/customer/{$this->company->id}/tax-invoices")->assertOk();
});

it('shows the invoice detail and the printable preview in sandboxed frames', function () {
    GdtSession::withoutGlobalScopes()->create(['company_id' => $this->company->id, 'token' => 't', 'expires_at' => now()->addHour()]);
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);
    Http::fake([
        '*/invoices/detail*' => Http::response(['khhdon' => 'C26TAA', 'shdon' => '1', 'hdhhdvu' => [['ten' => 'May tinh']]]),
        '*/invoices/export-xml*' => Http::response('no archive'),
    ]);

    $page = Livewire::test(ListTaxInvoices::class)->mountTableAction('detail', $invoice);
    $detail = modalHtml($page);
    expect($detail)->toContain('sandbox="allow-same-origin"')->toContain('May tinh');

    $page->unmountTableAction()->mountTableAction('preview', $invoice);
    expect(modalHtml($page))->toContain('In / Lưu PDF');
});

it('tells the user to reconnect when the portal session is gone', function () {
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);

    $page = Livewire::test(ListTaxInvoices::class)->mountTableAction('detail', $invoice);

    expect(modalHtml($page))->toContain('chưa kết nối');
});

it('streams a single invoice xml download', function () {
    GdtSession::withoutGlobalScopes()->create(['company_id' => $this->company->id, 'token' => 't', 'expires_at' => now()->addHour()]);
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);
    Http::fake(['*/invoices/export-xml*' => Http::response('<HDon/>')]);

    Livewire::test(ListTaxInvoices::class)
        ->callTableAction('xml', $invoice)
        ->assertFileDownloaded();
});
