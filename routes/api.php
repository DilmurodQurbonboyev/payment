<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\ClickController;

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


Route::post('/posts', [ProductController::class, 'productData']);

Route::controller(ClickController::class)
    ->middleware('clickSignString')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::post('/click-prepare', 'prepare');
        Route::post('/click-complete', 'complete');
    });
