<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::prefix('admin')->group(function () {


Route::get('login', [App\Http\Controllers\Auth\AuthController::class, 'index'])->name('login');
Route::post('post-login', [App\Http\Controllers\Auth\AuthController::class, 'postLogin'])->name('login.post'); 
//Route::get('/registration', [App\Http\Controllers\Auth\AuthController::class, 'registration'])->name('register');
//Route::post('post-registration', [App\Http\Controllers\Auth\AuthController::class, 'postRegistration'])->name('register.post'); 
Route::get('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

//Route::group(['middleware' => 'admin'], function(){
Route::get('dashboard', [App\Http\Controllers\Auth\AuthController::class, 'dashboard'])->name('admin.dashboard');

Route::get('products', [ProductController::class, 'index'])->name('admin.product');
Route::get('products/create', [ProductController::class, 'create'])->name('admin.product.create');
Route::post('products/store', [ProductController::class, 'store'])->name('admin.product.store');
Route::get('products/show/{product}', [ProductController::class, 'show'])->name('admin.product.show');
Route::delete('product/delete/{id}', [ProductController::class, 'destroy'])->name('admin.product.delete');


// users

Route::get('users', [UserController::class, 'index'])->name('admin.user');
Route::get('users/create', [UserController::class, 'create'])->name('admin.user.create');
Route::post('user/store', [UserController::class, 'store'])->name('admin.user.store');
Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
Route::post('users/update/{id}', [UserController::class, 'update'])->name('admin.user.update');

Route::delete('user/delete/{id}', [UserController::class, 'delete'])->name('admin.user.delete');
//});

});
