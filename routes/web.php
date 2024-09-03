<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers;
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
Route::prefix('/blogs')->group(function () {
    Route::get('/', Controllers\Articles\ListArticlesController::class)->name('blogs');
  //get / return view blogs
});
// Route::get('/{post:slug}', [BlogsController::class, 'show'])->name('admin.post.show');
//require __DIR__.'/auth.php';
