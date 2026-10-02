<?php

use App\Models\Contacts;
use App\Models\Product;
use Database\Seeders\AddonSeeder;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(AddonSeeder::class);
});

function makeAddon(array $overrides = []): Product
{
    return Product::create($overrides + [
        'name' => 'Addon thử nghiệm',
        'slug' => 'addon-thu-nghiem',
        'type' => Product::TYPE_ADDON,
        'integration' => 'Bank',
        'billing' => Product::BILLING_QUOTE,
        'price' => 0,
        'quantity' => 0,
        'status' => 'active',
        'sap_versions' => ['10.0'],
    ]);
}

it('lists every seeded addon on the marketplace', function () {
    $response = $this->get('/marketplace')->assertOk();

    foreach (Product::addons()->get() as $addon) {
        $response->assertSee($addon->name);
    }
    expect(Product::addons()->count())->toBe(6);
});

it('does not list physical products, inactive addons or drafts of other types', function () {
    makeAddon(['name' => 'Sản phẩm vật lý', 'slug' => 'san-pham-vat-ly', 'type' => Product::TYPE_PHYSICAL]);
    makeAddon(['name' => 'Addon đã ẩn', 'slug' => 'addon-da-an', 'status' => 'inactive']);

    $this->get('/marketplace')
        ->assertOk()
        ->assertDontSee('Sản phẩm vật lý')
        ->assertDontSee('Addon đã ẩn');
});

it('filters by integration', function () {
    $this->get('/marketplace?integration=Shopify')
        ->assertOk()
        ->assertSee('Tích hợp Shopify với SAP B1')
        ->assertDontSee('Tích hợp Magento với SAP B1')
        ->assertDontSee('Tích hợp SePay cho SAP B1');
});

it('filters by SAP version', function () {
    makeAddon(['name' => 'Chỉ bản 9.3', 'slug' => 'chi-ban-9-3', 'sap_versions' => ['9.3']]);
    makeAddon(['name' => 'Chỉ bản 10.0', 'slug' => 'chi-ban-10-0', 'sap_versions' => ['10.0']]);

    $this->get('/marketplace?sap_version=9.3')
        ->assertOk()
        ->assertSee('Chỉ bản 9.3')
        ->assertDontSee('Chỉ bản 10.0');
});

it('combines integration and SAP version filters', function () {
    makeAddon(['name' => 'Bank 9.3', 'slug' => 'bank-9-3', 'integration' => 'Bank', 'sap_versions' => ['9.3']]);
    makeAddon(['name' => 'Bank 10.0', 'slug' => 'bank-10-0', 'integration' => 'Bank', 'sap_versions' => ['10.0']]);

    $this->get('/marketplace?integration=Bank&sap_version=10.0')
        ->assertOk()
        ->assertSee('Bank 10.0')
        ->assertDontSee('Bank 9.3');
});

it('ignores unknown filter values instead of emptying the page', function () {
    $this->get('/marketplace?integration=Nope&sap_version=1.0')
        ->assertOk()
        ->assertSee('Tích hợp Shopify với SAP B1');
});

it('shows an empty state when nothing matches', function () {
    Product::query()->where('integration', 'Shopify')->update(['status' => 'inactive']);

    $this->get('/marketplace?integration=Shopify')
        ->assertOk()
        ->assertSee('Chưa có addon nào khớp bộ lọc');
});

it('shows an addon page with details and structured data', function () {
    $addon = Product::where('integration', 'Shopify')->firstOrFail();

    $response = $this->get('/marketplace/'.$addon->slug)
        ->assertOk()
        ->assertSee($addon->name)
        ->assertSee('Yêu cầu báo giá')
        ->assertSee('luồng dữ liệu')
        ->assertSee('câu hỏi thường gặp')
        ->assertSee('application/ld+json', false)
        ->assertSee('SoftwareApplication', false);

    // Quote-based addons must not advertise a price.
    $response->assertDontSee('"offers"', false);
});

it('publishes an offer only for addons with a real price', function () {
    makeAddon(['slug' => 'addon-co-gia', 'billing' => Product::BILLING_YEARLY, 'price' => 1500000]);

    $this->get('/marketplace/addon-co-gia')
        ->assertOk()
        ->assertSee('"offers"', false)
        ->assertSee('Đặt mua');
});

it('returns 404 for unknown, inactive or non-addon slugs', function () {
    makeAddon(['slug' => 'addon-an', 'status' => 'inactive']);
    makeAddon(['slug' => 'hang-vat-ly', 'type' => Product::TYPE_PHYSICAL]);

    $this->get('/marketplace/khong-ton-tai')->assertNotFound();
    $this->get('/marketplace/addon-an')->assertNotFound();
    $this->get('/marketplace/hang-vat-ly')->assertNotFound();
});

it('stores a quote request as a contact and notifies by email', function () {
    $addon = Product::where('integration', 'SePay')->firstOrFail();

    $this->post(route('marketplace.quote', $addon->slug), [
        'name' => 'Nguyễn Văn A',
        'email' => 'a@example.com',
        'phone' => '0900000000',
        'company' => 'Công ty A',
        'sap_version' => '10.0',
        'db' => 'hana',
        'message' => 'Cần đối soát thanh toán tự động.',
    ])->assertRedirect(route('marketplace.show', $addon->slug).'#quote')
        ->assertSessionHas('quote_sent');

    $contact = Contacts::firstOrFail();
    expect($contact->full_name)->toBe('Nguyễn Văn A')
        ->and($contact->email)->toBe('a@example.com')
        ->and($contact->company_name)->toBe('Công ty A')
        ->and($contact->topic)->toBe($addon->slug)
        ->and($contact->message)->toContain($addon->name)
        ->and($contact->message)->toContain('10.0')
        ->and($contact->message)->toContain('SAP HANA')
        ->and($contact->message)->toContain('Cần đối soát thanh toán tự động.')
        ->and($contact->contactReason->name)->toBe('Yêu cầu báo giá addon');

    // One notification to the admin and one auto-reply to the visitor.
    expect(app('mail.manager')->mailer()->getSymfonyTransport()->messages())->toHaveCount(2);

    $this->get('/marketplace/'.$addon->slug)->assertSee('Đã nhận yêu cầu');
});

it('rejects an invalid quote request without saving anything', function () {
    $addon = Product::firstWhere('integration', 'SePay');

    $this->from(route('marketplace.show', $addon->slug))
        ->post(route('marketplace.quote', $addon->slug), [
            'name' => '',
            'email' => 'not-an-email',
            'sap_version' => '1.0',
        ])
        ->assertRedirect(route('marketplace.show', $addon->slug).'#quote')
        ->assertSessionHasErrors(['name', 'email', 'sap_version']);

    expect(Contacts::count())->toBe(0);
});

it('drops quote requests that fill the honeypot field', function () {
    $addon = Product::firstWhere('integration', 'SePay');

    $this->post(route('marketplace.quote', $addon->slug), [
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'website' => 'http://spam.example',
    ])->assertSessionHas('quote_sent');

    expect(Contacts::count())->toBe(0);
});

it('does not accept quotes for unknown addons', function () {
    $this->post(route('marketplace.quote', 'khong-co'), [
        'name' => 'A',
        'email' => 'a@example.com',
    ])->assertNotFound();
});
