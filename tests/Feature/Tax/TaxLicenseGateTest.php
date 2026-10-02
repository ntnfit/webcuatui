<?php

use App\Filament\Customer\Resources\TaxInvoices\Pages\ListTaxInvoices;
use App\Filament\Resources\TaxLicenses\Pages\CreateTaxLicense;
use App\Filament\Resources\TaxLicenses\Pages\ListTaxLicenses;
use App\Jobs\RunTaxExport;
use App\Models\Company;
use App\Models\Customer;
use App\Models\GdtSession;
use App\Models\TaxExportRun;
use App\Models\TaxInvoice;
use App\Models\TaxLicense;
use App\Models\User;
use App\Services\Gdt\Export\ExportRequestService;
use App\Services\Gdt\Export\ExportRunner;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtSessionService;
use App\Services\Gdt\InvoiceSearch;
use App\Services\Gdt\InvoiceSyncService;
use App\Services\Gdt\LicenseNotice;
use App\Services\Gdt\LicenseRequiredException;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
    GdtClient::$initialDelayMs = 0;
    Storage::fake('local');
    Http::fake(['*' => Http::response(['datas' => [], 'token' => 'a.b.c'])]);
});

function connected(Company $company): Company
{
    GdtSession::withoutGlobalScopes()->create(['company_id' => $company->id, 'token' => 't', 'expires_at' => now()->addHour()]);

    return $company;
}

function lookup(): InvoiceSearch
{
    return new InvoiceSearch('purchase', Carbon::now()->subDays(10), Carbon::now());
}

dataset('unlicensed', [
    'no license' => fn () => Company::factory()->create(),
    'expired' => fn () => Company::factory()->has(TaxLicense::factory()->expired(), 'taxLicenses')->create(),
    'revoked' => fn () => Company::factory()->has(TaxLicense::factory()->revoked(), 'taxLicenses')->create(),
    'not started' => fn () => Company::factory()->has(TaxLicense::factory()->state(['starts_at' => now()->addDay()]), 'taxLicenses')->create(),
]);

it('blocks portal sync without a valid license', function (Company $company) {
    connected($company);

    expect(fn () => app(InvoiceSyncService::class)->sync($company, lookup()))
        ->toThrow(LicenseRequiredException::class, 'chưa kích hoạt hoặc đã hết hạn');
    Http::assertNothingSent();
    expect($company->hasValidLicense())->toBeFalse();
})->with('unlicensed');

it('blocks export requests without a valid license', function (Company $company) {
    connected($company);

    expect(fn () => app(ExportRequestService::class)->request($company, $company->owner, 'excel', lookup()))
        ->toThrow(LicenseRequiredException::class);
    expect(TaxExportRun::withoutGlobalScopes()->count())->toBe(0);
})->with('unlicensed');

it('hides cached invoices and denies invoice access without a valid license', function (Company $company) {
    $invoice = TaxInvoice::factory()->create(['company_id' => $company->id]);
    $this->actingAs($company->owner, 'customer');
    Filament::setCurrentPanel('customer');
    Filament::setTenant($company);

    Livewire::test(ListTaxInvoices::class)->assertCanNotSeeTableRecords([$invoice]);
    expect($company->owner->can('view', $invoice))->toBeFalse();
})->with('unlicensed');

it('allows sync, export and list with a valid license', function () {
    $company = connected(Company::factory()->licensed()->create());
    TaxInvoice::factory()->create(['company_id' => $company->id, 'issued_at' => now()->subDay(), 'detail' => ['hdhhdvu' => []]]);

    expect(app(InvoiceSyncService::class)->sync($company, lookup())['count'])->toBe(0);
    $run = app(ExportRequestService::class)->request($company, $company->owner, 'excel', lookup());
    expect($run->fresh()->status)->toBe('done');

    $this->actingAs($company->owner, 'customer');
    Filament::setCurrentPanel('customer');
    Filament::setTenant($company);
    Livewire::test(ListTaxInvoices::class)->assertCanSeeTableRecords(TaxInvoice::all());
});

it('fails a queued export gracefully when the license expired before it ran', function () {
    $company = connected(Company::factory()->licensed()->create());
    TaxInvoice::factory()->create(['company_id' => $company->id, 'issued_at' => now()->subDay()]);
    // Only the export job is held back; Filament's notification job must still run.
    Queue::fake([RunTaxExport::class]);

    $run = app(ExportRequestService::class)->request($company, $company->owner, 'xml_zip', lookup());
    Queue::assertPushed(RunTaxExport::class);

    TaxLicense::query()->update(['expires_at' => now()->subMinute()]);
    (new RunTaxExport($run->id))->handle(app(ExportRunner::class));

    $run = $run->fresh();
    expect($run->status)->toBe('failed')->and($run->error)->toContain('hết hạn')->and($run->file_path)->toBeNull();
    Http::assertNothingSent();
    expect($company->owner->notifications()->count())->toBe(1);
});

it('extends access with a renewal row and reports the latest expiry', function () {
    $company = Company::factory()->has(TaxLicense::factory()->expired(), 'taxLicenses')->create();
    expect($company->hasValidLicense())->toBeFalse();

    TaxLicense::factory()->create(['company_id' => $company->id, 'starts_at' => now()->subDay(), 'expires_at' => now()->addDays(40)]);
    TaxLicense::factory()->create(['company_id' => $company->id, 'expires_at' => now()->addDays(10)]);

    expect($company->hasValidLicense())->toBeTrue()
        ->and($company->activeLicense()->expires_at->isSameDay(now()->addDays(40)))->toBeTrue()
        ->and($company->licenseDaysLeft())->toBe(40);
});

it('keeps a still-running license when a renewal has not started yet', function () {
    $company = Company::factory()->licensed()->create();
    TaxLicense::factory()->create(['company_id' => $company->id, 'starts_at' => now()->addYear(), 'expires_at' => now()->addYears(2)]);

    expect($company->hasValidLicense())->toBeTrue();
});

it('never lets one company license grant access to another', function () {
    $licensed = Company::factory()->licensed()->create();
    $other = connected(Company::factory()->create());

    expect($licensed->hasValidLicense())->toBeTrue()
        ->and($other->hasValidLicense())->toBeFalse()
        ->and(fn () => app(GdtSessionService::class)->clientFor($other))->toThrow(LicenseRequiredException::class);
});

it('still lets an unlicensed company register data and connect to the portal', function () {
    $company = Company::factory()->create();

    $session = app(GdtSessionService::class)->connect($company, 'u', 'p', 'c', 'k');

    expect($session->company_id)->toBe($company->id);
});

it('shows a notice when blocked and a warning style when 14 days or less remain', function () {
    $none = Company::factory()->create();
    expect(LicenseNotice::bannerHtml($none))->toContain('chưa kích hoạt hoặc đã hết hạn')->toContain(LicenseNotice::CONTACT_EMAIL);

    $soon = Company::factory()->create();
    TaxLicense::factory()->create(['company_id' => $soon->id, 'expires_at' => now()->addDays(5)]);
    expect(LicenseNotice::bannerHtml($soon))->toContain('#b45309')->toContain('gia hạn');

    $fine = Company::factory()->licensed()->create();
    expect(LicenseNotice::bannerHtml($fine))->toContain('#15803d')->not->toContain('gia hạn');
});

it('states plainly whether the license is valid and until when', function () {
    $none = Company::factory()->create();
    expect(LicenseNotice::bannerHtml($none))->toContain('Giấy phép không hợp lệ');

    $fine = Company::factory()->create();
    TaxLicense::factory()->create(['company_id' => $fine->id, 'expires_at' => now()->addDays(40)]);
    expect(LicenseNotice::bannerHtml($fine))
        ->toContain('Giấy phép hợp lệ')
        ->toContain(now()->addDays(40)->format('d/m/Y'));
});

it('renders the banner on the tool pages and lands on them without a dashboard', function () {
    $company = Company::factory()->create();
    $this->actingAs($company->owner, 'customer');

    $this->get("/customer/{$company->id}/connect-gdt")->assertOk()->assertSee('Giấy phép không hợp lệ');
    $this->get("/customer/{$company->id}")->assertRedirect();
});

it('serves the customer sign-in and registration pages in Vietnamese', function () {
    $this->get('/customer/login')->assertOk()->assertSee('Đăng nhập');
    $this->get('/customer/register')->assertOk()->assertSee('Đăng ký');
});

it('lets an admin assign a license and records who created it', function () {
    $admin = User::factory()->create();
    $company = Company::factory()->create();
    $this->actingAs($admin);
    Filament::setCurrentPanel('admin');

    Livewire::test(CreateTaxLicense::class)
        ->fillForm(['company_id' => $company->id, 'starts_at' => '2026-10-01', 'expires_at' => '2027-10-01', 'status' => 'active', 'notes' => 'goi nam'])
        ->call('create')
        ->assertHasNoFormErrors();

    $license = TaxLicense::sole();
    expect($license->created_by)->toBe($admin->id)
        ->and($license->expires_at->format('Y-m-d H:i'))->toBe('2027-10-01 23:59');
});

it('lists licenses for admins with the expiring filter', function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
    $soon = TaxLicense::factory()->create(['expires_at' => now()->addDays(10)]);
    $later = TaxLicense::factory()->create();

    Livewire::test(ListTaxLicenses::class)
        ->assertCanSeeTableRecords([$soon, $later])
        ->filterTable('expiring', true)
        ->assertCountTableRecords(1);
});

it('keeps customers away from license administration', function () {
    $customer = Customer::factory()->create();
    $license = TaxLicense::factory()->create();

    expect($customer->can('create', TaxLicense::class))->toBeFalse()
        ->and($customer->can('update', $license))->toBeFalse()
        ->and($customer->can('viewAny', TaxLicense::class))->toBeFalse();

    $this->actingAs($customer, 'customer');
    $this->get('/admin/tax-licenses')->assertRedirect();
    $this->get('/admin/tax-licenses/create')->assertRedirect();
    expect(TaxLicense::count())->toBe(1);
});
