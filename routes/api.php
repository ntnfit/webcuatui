<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogApiController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/contact', [ContactController::class, 'store']);

// Blog API v1
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/blog', [BlogApiController::class, 'index']);
    Route::get('/blog/{slug}', [BlogApiController::class, 'show']);

    // Protected routes (require Basic Authentication)
    Route::middleware('auth.basic')->group(function () {
        Route::post('/blog', [BlogApiController::class, 'store']);
        Route::put('/blog/{id}', [BlogApiController::class, 'update']);
        Route::delete('/blog/{id}', [BlogApiController::class, 'destroy']);
    });
});
