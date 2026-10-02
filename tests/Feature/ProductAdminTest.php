<?php

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->actingAs(User::factory()->create());
});

it('creates an addon with marketplace fields from the admin form', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'type' => Product::TYPE_ADDON,
            'name' => 'Addon từ admin',
            'slug' => 'addon-tu-admin',
            'price' => 0,
            'quantity' => 0,
            'status' => 'active',
            'integration' => 'Shopify',
            'billing' => Product::BILLING_QUOTE,
            'sap_versions' => ['10.0'],
            'db_support' => ['hana'],
            'summary' => 'Tóm tắt',
            'features' => [['feature' => 'Đồng bộ tồn kho']],
            'faqs' => [['q' => 'Hỏi?', 'a' => 'Đáp.']],
            'docs_url' => 'https://example.com/docs',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $addon = Product::where('slug', 'addon-tu-admin')->firstOrFail();

    expect($addon->type)->toBe(Product::TYPE_ADDON)
        ->and($addon->integration)->toBe('Shopify')
        ->and($addon->sap_versions)->toBe(['10.0'])
        ->and($addon->db_support)->toBe(['hana'])
        ->and($addon->features)->toBe(['Đồng bộ tồn kho'])
        ->and($addon->faqs[0]['q'])->toBe('Hỏi?');

    $this->get('/marketplace/addon-tu-admin')->assertOk()->assertSee('Addon từ admin');
});

it('requires an integration for addons', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'type' => Product::TYPE_ADDON,
            'name' => 'Thiếu tích hợp',
            'slug' => 'thieu-tich-hop',
            'price' => 0,
            'quantity' => 0,
            'status' => 'active',
            'billing' => Product::BILLING_QUOTE,
        ])
        ->call('create')
        ->assertHasFormErrors(['integration' => 'required']);
});

it('loads an existing addon in the edit form', function () {
    $addon = Product::create([
        'name' => 'Addon sửa', 'slug' => 'addon-sua', 'type' => Product::TYPE_ADDON, 'integration' => 'Bank',
        'billing' => Product::BILLING_QUOTE, 'price' => 0, 'quantity' => 0, 'status' => 'active',
        'sap_versions' => ['9.3'],
    ]);

    Livewire::test(EditProduct::class, ['record' => $addon->getKey()])
        ->assertFormSet(['integration' => 'Bank', 'sap_versions' => ['9.3']]);
});
