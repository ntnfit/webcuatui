<?php


use App\Http\Controllers\Admin\TinyMceUploadController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// TinyMCE image upload — signed URL enforces disk/directory; auth guards admin-only access
Route::post('/admin/tinymce/upload', [TinyMceUploadController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('tinymce.upload');

Route::get('/test', [BlogsController::class, 'index']);
Route::get('/', HomeController::class)->name('home');

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

