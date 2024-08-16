<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders-detail/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');

    Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');

    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove{productId}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/complete', [App\Http\Controllers\OdemeController::class, 'odeme'])->name('cart.complete');
    Route::post('/paytr/bildirim', [App\Http\Controllers\OdemeController::class, 'bildirim'])->name('paytr.callback');

    Route::get('/odeme',[App\Http\Controllers\OdemeController::class, 'index'])->name('odeme.index');
    Route::post('/sonuc', [App\Http\Controllers\OdemeController::class, 'odeme'])->name('odeme.sonuc'); // ödememe formu getirme
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
