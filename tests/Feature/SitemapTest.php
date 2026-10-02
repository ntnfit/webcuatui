<?php

use App\Models\Product;
use Database\Seeders\AddonSeeder;

it('lists static pages and marketplace addons in the sitemap', function () {
    $this->seed(AddonSeeder::class);
    Product::where('integration', 'Bank')->update(['status' => 'inactive']);

    $response = $this->get('/sitemap.xml')->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('xml');

    $response->assertSee(url('/marketplace'), false)
        ->assertSee(url('/tools'), false)
        ->assertSee(url('/blogs'), false)
        ->assertSee(route('marketplace.show', 'tich-hop-shopify-sap-b1'), false)
        ->assertDontSee('tich-hop-ngan-hang-sap-b1', false);
});
