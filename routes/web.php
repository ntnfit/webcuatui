<?php

use App\Http\Controllers\Admin\TinyMceUploadController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ToolsController;
use Illuminate\Support\Facades\Route;

// TinyMCE image upload — signed URL enforces disk/directory; auth guards admin-only access
Route::post('/admin/tinymce/upload', [TinyMceUploadController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('tinymce.upload');

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'show'])->name('sitemap');

// Marketplace (SAP B1 addons)
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{slug}', [MarketplaceController::class, 'show'])->name('marketplace.show');
Route::post('/marketplace/{slug}/quote', [MarketplaceController::class, 'quote'])
    ->middleware('throttle:6,1')
    ->name('marketplace.quote');

// Tools
Route::get('/tools', [ToolsController::class, 'index'])->name('tools.index');

// Blog routes
Route::get('/blogs', [BlogsController::class, 'index'])->name('blogs.index');

Route::get('/blogs/{slug}', [BlogsController::class, 'show'])->name('blogs.show');

// Shop routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [ShopController::class, 'cart'])->name('cart');
Route::post('/api/cart/items', [ShopController::class, 'getCartItems'])->name('cart.items');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [ShopController::class, 'processCheckout'])->name('checkout.process');
