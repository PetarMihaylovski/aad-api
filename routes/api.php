<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImageUploadController;

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

Route::get('/shops/{id}', [ShopController::class, 'show']);
Route::get('/shops/{id}/products', [ProductController::class, 'show']);

//user routes
Route::post('/register', [UserController::class, 'store']);
Route::get('/login', [UserController::class, 'login']);


//product routes
Route::get('/products', [ProductController::class, 'index']);


Route::post('/product/image', [ImageUploadController::class, 'uploadProductImages']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/shops', [ShopController::class, 'store']);


    //ToDo change 1 of the 2 routes
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/one', [ProductController::class, 'storeOneProduct']);
});
