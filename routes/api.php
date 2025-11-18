<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\demoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
| https://www.positronx.io/laravel-jwt-authentication-tutorial-user-login-signup-api/
*/



Route::group([
    'middleware' => 'api',
    //'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('app.login');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);

    //Route::apiResource('products', ProductController::class);
    Route::get('products', [ProductController::class, 'index'])->name('products.list');
    
    Route::get('getallproduct', [ProductController::class, 'getallproduct']);

    Route::post('product-create', [ProductController::class, 'store']);
    
    Route::get('product-search/{text}', [ProductController::class, 'search']);
    Route::post('product-search-new', [ProductController::class, 'searchnew']);

    //Route::put('product-update', [ProductController::class, 'update']);
    Route::post('product-update/{id}', [ProductController::class, 'update']);

    Route::get('product-delete/{id}',[ProductController::class, 'delete']);
    
    Route::get('getalldevice', [ProductController::class, 'getalldevice']);
    Route::post('device-update', [ProductController::class, 'updatedevice']);

});





