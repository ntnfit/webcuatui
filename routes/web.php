<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogsController;
use Inertia\Inertia;

Route::get('/test', [BlogsController::class, 'index']);
Route::get('/', function () {
    $posts = \App\Models\blogs::with(['user', 'categories', 'tags'])->published()->take(6)->get();
    return Inertia::render('Index', [
        'latestArticles' => $posts->map(fn ($post) => $post->getDataArray()),
    ]);
})->name('home');

// Blog routes
Route::get('/blogs', [BlogsController::class, 'index'])->name('blogs.index');

Route::get('/blogs/{slug}', [BlogsController::class, 'show'])->name('blogs.show');
// Blog detail route with slug - phải đặt trước các route khác có pattern tương tự
//Route::get('/blogss/{slug}', [BlogController::class, 'show'])->name('blogs.show');

// // 404 route - must be the last route
// Route::fallback(function () {
//     return Inertia::render('NotFound');
// })->name('not-found');




