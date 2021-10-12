<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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

//Examples
/*
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);
Route::post('/rooms', [RoomController::class, 'store']);
Route::put('/rooms/{room}', [RoomController::class, 'update']);
Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);
Route::get('/rooms/search/{date}', [RoomController::class, 'search']);*/

//shop routes
Route::middleware(['cors'])->group(function () {
    Route::get('/shops', [ShopController::class, 'index']);
});

Route::post('/shops', [ShopController::class, 'store']);
Route::get('/shops/{id}', [ShopController::class, 'show']);
Route::get('/shops/{id}/products', [ShopController::class, 'getAllProdutsFromShop']);

//user routes
Route::post('/users', [UserController::class, 'store']);
//testing
Route::get('/users', [UserController::class, 'index']);

//product routes
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {
});
