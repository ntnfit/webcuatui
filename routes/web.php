<?php


use App\Http\Controllers\BlogsController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/test', [BlogsController::class, 'index']);
Route::get('/', function () {
    $posts = \App\Models\blogs::with(['user', 'categories', 'tags'])->published()->take(6)->get();

    return view('home', [
        'latestArticles' => $posts->map(fn ($post) => $post->getDataArray()),
    ]);
})->name('home');

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

// // 404 route - must be the last route
// Route::fallback(function () {
//     return Inertia::render('NotFound');
// })->name('not-found');
