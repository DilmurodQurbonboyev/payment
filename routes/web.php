<?php

use App\Http\Controllers\ClickController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(ClickController::class)
    ->middleware('clickSignString')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::post('/click-prepare', 'prepare');
        Route::post('/click-complete', 'complete');
    });
