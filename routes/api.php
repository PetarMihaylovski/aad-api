<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// unrestricted

//shop
Route::get('/shops', [ShopController::class, 'index']);
Route::get('/shops/{id}', [ShopController::class, 'show']);

// product
Route::get('/shops/{id}/products', [ProductController::class, 'show']);

//auth
Route::post('/register', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

// restricted
Route::group(['middleware' => ['auth:sanctum']], function () {
    //auth
    Route::get('/logout', [AuthController::class, 'logout']);

    //shop routes
    Route::post('/shops', [ShopController::class, 'store']);
    Route::patch('/shops/{id}', [ShopController::class, 'update']);
    Route::delete('/shops/{id}', [ShopController::class, 'destroy']);

    //product routes
    Route::post('/shops/{shopId}/products', [ProductController::class, 'store']);
    Route::patch('/shops/{shopId}/products/{productId}', [ProductController::class, 'update']);
    Route::delete('/shops/{shopId}/products/{productId}', [ProductController::class, 'destroy']);

    //order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
});
