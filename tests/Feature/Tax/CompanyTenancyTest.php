<?php

use App\Filament\Customer\Pages\Tenancy\RegisterCompany;
use App\Models\Company;
use App\Models\Customer;
use App\Models\TaxInvoice;
use Filament\Auth\Pages\Register;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    // Error pages render through the public layout, which needs the Vite manifest.
    $this->withoutVite();
    Filament::setCurrentPanel('customer');
});

it('serves the customer registration and login pages', function () {
    $this->get('/customer/register')->assertOk();
    $this->get('/customer/login')->assertOk();
});

it('registers a customer through the panel form', function () {
    Livewire::test(Register::class)
        ->fillForm([
            'name' => 'Nguyen Van A',
            'email' => 'a@example.com',
            'password' => 'secret-pass-123',
            'passwordConfirmation' => 'secret-pass-123',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    expect(Customer::where('email', 'a@example.com')->exists())->toBeTrue();
});

it('lets a customer create two companies and become owner of both', function () {
    $customer = Customer::factory()->create();
    $this->actingAs($customer, 'customer');

    foreach (['0100000001', '0100000002'] as $mst) {
        Livewire::test(RegisterCompany::class)
            ->fillForm(['name' => "Cong ty $mst", 'mst' => $mst])
            ->call('register')
            ->assertHasNoFormErrors();
    }

    expect($customer->companies()->count())->toBe(2)
        ->and($customer->companies()->wherePivot('role', 'owner')->count())->toBe(2);
});

it('rejects a duplicate tax code for the same owner but allows it for another owner', function () {
    $customer = Customer::factory()->create();
    Company::factory()->create(['owner_id' => $customer->id, 'mst' => '0100000001']);
    $this->actingAs($customer, 'customer');

    Livewire::test(RegisterCompany::class)
        ->fillForm(['name' => 'Trung', 'mst' => '0100000001'])
        ->call('register')
        ->assertHasFormErrors(['mst']);

    $other = Customer::factory()->create();
    $this->actingAs($other, 'customer');

    Livewire::test(RegisterCompany::class)
        ->fillForm(['name' => 'Khac chu', 'mst' => '0100000001'])
        ->call('register')
        ->assertHasNoFormErrors();
});

it('rejects a malformed tax code', function () {
    $this->actingAs(Customer::factory()->create(), 'customer');

    Livewire::test(RegisterCompany::class)
        ->fillForm(['name' => 'Sai', 'mst' => '12ab'])
        ->call('register')
        ->assertHasFormErrors(['mst']);
});

it('denies access to a company the customer does not belong to', function () {
    $mine = Company::factory()->create();
    $theirs = Company::factory()->create();
    $customer = $mine->owner;

    $this->actingAs($customer, 'customer');

    $this->get("/customer/{$mine->id}")->assertOk();
    // Another company's tenant URL must not reveal that the company exists.
    $this->get("/customer/{$theirs->id}")->assertNotFound();
    expect($customer->canAccessTenant($theirs))->toBeFalse();
});

it('scopes tax invoices to the active tenant and policy', function () {
    $mine = Company::factory()->licensed()->create();
    $theirs = Company::factory()->licensed()->create();
    TaxInvoice::factory()->create(['company_id' => $mine->id]);
    TaxInvoice::factory()->count(2)->create(['company_id' => $theirs->id]);

    $this->actingAs($mine->owner, 'customer');
    Filament::setTenant($mine);

    expect(TaxInvoice::count())->toBe(1);

    $foreign = TaxInvoice::withoutGlobalScopes()->where('company_id', $theirs->id)->first();
    expect($mine->owner->can('view', $foreign))->toBeFalse()
        ->and($theirs->owner->can('view', $foreign))->toBeTrue();
});

it('only lets the owner update the company', function () {
    $company = Company::factory()->create();
    $member = Customer::factory()->create();
    $company->members()->attach($member->id, ['role' => 'member']);

    expect($member->can('view', $company))->toBeTrue()
        ->and($member->can('update', $company))->toBeFalse()
        ->and($company->owner->can('update', $company))->toBeTrue();
});
