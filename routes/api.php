<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RFQController;
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

# products
Route::prefix('products')->group(function () {
    // Get products
    Route::get('/', [ProductController::class, 'index']);
    // Create product
    Route::post('/', [ProductController::class, 'store'])->middleware('jwt.auth');
});

# rfqs
Route::prefix('rfqs')->group(function () {
    // Get rfqs
    Route::get('/', [RFQController::class, 'index']);
    // Create rfq
    Route::post('/', [RFQController::class, 'store'])->middleware('jwt.auth');
    // Accept rfq
    Route::put('/{id}/accept', [RFQController::class, 'accept'])->middleware('jwt.auth');
});
