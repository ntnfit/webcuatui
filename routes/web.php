<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\Articles\ListArticlesController;
use App\Http\Controllers\Articles\ViewArticleController;
use Illuminate\Support\Facades\App;

Route::view('/', 'home')->name('home');

Route::view('/cong-cu', 'tools')->name('tools');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // blogs here

});

// Route::get('/tags/{tag:slug}', [TagController::class, 'posts'])->name('tag.post');
Route::prefix('/blogs')->group(function () {
    Route::get('/', ListArticlesController::class)->name('blogs');
});
Route::name('admin.')->prefix('/{blogs:slug}')->group(function () {
    Route::get('/', ViewArticleController::class)->name('post.show');
});



