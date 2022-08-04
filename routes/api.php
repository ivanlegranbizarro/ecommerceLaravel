<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;


/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | is assigned the "api" middleware group. Enjoy building your API! | */


/*Auth routes */
Route::post('auth/login', [AuthController::class , 'login'])->name('api.login');
Route::post('auth/register', [AuthController::class , 'register'])->name('api.register');

/*Categories routes */
Route::get('categories', [CategoryController::class , 'index'])->name('api.categories.index');
Route::get('categories/{category}', [CategoryController::class , 'show'])->name('api.categories.show');

/*Products routes */
Route::get('products', [ProductController::class , 'index'])->name('api.products.index');
Route::get('products/{product}', [ProductController::class , 'show'])->name('api.products.show');

/*Need to be authenticated */
Route::group(['middleware' => 'auth:api'], function () {

  /*Auth routes */
  Route::get('auth/me', [AuthController::class , 'me'])->name('api.auth.me');
  Route::post('auth/logout', [AuthController::class , 'logout'])->name('api.auth.logout');

  /*Cart routes */
  Route::post('cart', [CartController::class , 'store'])->name('api.cart.store');
  Route::put('cart/{cart}', [CartController::class , 'update'])->name('api.cart.update');
  Route::delete('cart/{cart}', [CartController::class , 'destroy'])->name('api.cart.destroy');
  Route::get('cart/{cart}', [CartController::class , 'show'])->name('api.cart.show');

  /*Orders routes */
  Route::get('orders', [OrderController::class , 'index'])->name('api.orders.index');
  Route::post('orders', [OrderController::class , 'store'])->name('api.orders.store');

  /*Need to have 'admin' role */
  Route::group(['middleware' => 'isAdmin'], function () {
      /*Categories routes */
      Route::post('categories', [CategoryController::class , 'store'])->name('api.categories.store');
      Route::put('categories/{category}', [CategoryController::class , 'update'])->name('api.categories.update');
      Route::delete('categories/{category}', [CategoryController::class , 'destroy'])->name('api.categories.destroy');

      /*Products routes */
      Route::post('products', [ProductController::class , 'store'])->name('api.products.store');
      Route::put('products/{product}', [ProductController::class , 'update'])->name('api.products.update');
      Route::delete('products/{product}', [ProductController::class , 'destroy'])->name('api.products.destroy');

    }
    );
  });
