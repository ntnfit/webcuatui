<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\Articles\ListArticlesController;
use App\Http\Controllers\Articles\ViewArticleController;
use App\Http\Controllers\ToolsController;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ContactController;

Route::view('/', 'home')->name('home');

require_once __DIR__ . '/auth.php';
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // blogs here

});
Route::get('/sitemap.xml', [
    SitemapController::class,
    'generateSitemap'
]);
//
Route::name('contact.')->prefix('lien-he')->group(function () {
    Route::get('/', function () {
        return view('contact');
    })->name('index');
    Route::post('/contact', [ContactController::class, 'store'])->name('store');
    route::get('/check-var-sao-ke', function () {
        return view('tools.saoke');
    })->name('saoke');
    Route::get('/search-var', [ToolsController::class, 'search'])->name('search.var');
});
Route::name('aitools.')->prefix('ai-tool')->group(function () {
    Route::get('/', function () {
        return view('aitools');
    })->name('index');
});
Route::name('shop.')->prefix('shop')->group(function () {
    Route::get('/', function () {
        return view('contact');
    })->name('index');
});


Route::get ('/test', function(){
    return view('test');
});

// Route::get('/tags/{tag:slug}', [TagController::class, 'posts'])->name('tag.post');
Route::prefix('/blogs')->group(function () {
    Route::get('/', ListArticlesController::class)->name('blogs');
});
Route::name('admin.')->prefix('/{blogs:slug}')->group(function () {
    Route::get('/', ViewArticleController::class)->name('post.show');
});





