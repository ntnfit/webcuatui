<?php

use App\Enums\PostStatus;
use App\Models\blogs;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AddonSeeder;

beforeEach(function () {
    $this->withoutVite();
});

function publishedPost(array $overrides = []): blogs
{
    $user = User::factory()->create();

    return blogs::create($overrides + [
        'title' => 'Bài viết thử nghiệm',
        'slug' => 'bai-viet-thu-nghiem',
        'sub_title' => 'Mô tả ngắn',
        'body' => '<p>Nội dung</p>',
        'status' => PostStatus::PUBLISHED,
        'published_at' => now()->subDay(),
        'cover_photo_path' => '',
        'photo_alt_text' => 'alt',
        'user_id' => $user->id,
        'type' => 'article',
    ]);
}

it('renders the home page with addons and without ads or livewire assets', function () {
    $this->seed(AddonSeeder::class);

    $this->get('/')
        ->assertOk()
        ->assertSee('Tích hợp Shopify với SAP B1')
        ->assertSee('Marketplace')
        ->assertSee('Tools')
        ->assertSee('Blog')
        ->assertSee('Liên hệ')
        ->assertDontSee('adsbygoogle', false)
        ->assertDontSee('livewire', false);
});

it('shows the navbar on every public page', function (string $path) {
    $this->get($path)
        ->assertOk()
        ->assertSee(route('marketplace.index'), false)
        ->assertSee(route('tools.index'), false)
        ->assertSee(route('blogs.index'), false);
})->with(['/', '/marketplace', '/tools', '/blogs', '/shop']);

it('links the tools landing page to the tax tool', function () {
    $this->get('/tools')
        ->assertOk()
        ->assertSee('Công cụ thuế')
        ->assertSee(url('/customer'), false);
});

it('no longer exposes the stray test route', function () {
    $this->get('/test')->assertNotFound();
});

it('serves blog posts without a cover image and without fake data', function () {
    $post = publishedPost();

    $data = $post->load(['user', 'categories', 'tags'])->getDataArray();
    expect($data['thumbnail_url'])->toBeNull()
        ->and($data['stars'])->toBe(0)
        ->and($data['author']['avatar'])->toBeNull();

    $this->get('/')->assertOk()->assertSee('Bài viết thử nghiệm');
    $this->get('/blogs')->assertOk();
    $this->get('/blogs/'.$post->slug)
        ->assertOk()
        ->assertSee('Bài viết thử nghiệm')
        ->assertSee('adsbygoogle', false);
});

it('builds a real thumbnail url when a cover exists', function () {
    $post = publishedPost(['slug' => 'co-anh', 'title' => 'Có ảnh', 'cover_photo_path' => 'blog-feature-images/a.jpg']);

    expect($post->getDataArray()['thumbnail_url'])->toEndWith('/storage/blog-feature-images/a.jpg');
});

it('keeps the shop working for physical products and hides addons from it', function () {
    $this->seed(AddonSeeder::class);
    Product::create([
        'name' => 'Bàn phím', 'slug' => 'ban-phim', 'price' => 100000, 'quantity' => 5, 'status' => 'active',
    ]);

    $this->get('/shop')->assertOk()->assertSee('Bàn phím')->assertDontSee('Tích hợp Shopify với SAP B1');
    $this->get('/shop/ban-phim')->assertOk()->assertSee('Bàn phím');
    $this->get('/shop/tich-hop-shopify-sap-b1')->assertNotFound();
});
