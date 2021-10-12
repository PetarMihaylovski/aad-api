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
Route::post('/shop', [ShopController::class, 'store']);
Route::get('/shop/{id}', [ShopController::class, 'show']);
Route::get('/shop', [ShopController::class, 'index']);
Route::get('/shop/{id}/product', [ShopController::class, 'getAllProdutsFromShop']);

//user routes
Route::post('/user', [UserController::class, 'store']);
//testing
Route::get('/user', [UserController::class, 'index']);

//product routes
Route::get('/product', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {
});
