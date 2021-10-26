<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImageUploadController;
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

//shop routes
Route::middleware(['cors'])->group(function () {
    Route::get('/shops', [ShopController::class, 'index']);
});




//product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/shops/{id}', [ShopController::class, 'show']);
Route::get('/shops/{id}/products', [ProductController::class, 'show']);

//user routes
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);




Route::group(['middleware' => ['auth:sanctum']], function () {
    //user routes
    Route::get('/logout', [UserController::class, 'logout']);

    //shop routes
    Route::post('/shops', [ShopController::class, 'store']);
    Route::post('/shops/{id}', [ShopController::class, 'update']);

    //product routes
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/products/{query}', [ProductController::class, 'filter']);

    //order routes
    Route::post('/orders', [OrderController::class, 'store']);
});
