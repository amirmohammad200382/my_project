<?php

use App\Http\Controllers\ApiLoginController;
use App\Http\Controllers\ApiOrderController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [ApiLoginController::class, 'login'])->name('login');
Route::get('/filter', [ApiLoginController::class, 'filter'])->name('filter');
Route::post('/register', [ApiLoginController::class, 'register'])->name('register');
Route::get('auth/logout/{id}', [ApiLoginController::class, 'logout'])->name('logout');

//product
Route::prefix('products')->group(function () {
    Route::get('/', [ApiProductController::class, 'index'])->name('products_index')->middleware('Permission:products_index');
    Route::get('/filter', [ApiProductController::class, 'filter'])->name('products.filter');
    Route::post('/', [ApiProductController::class, 'store'])->name('products_store')->middleware('Permission:products_store');
    Route::put('/{id}', [ApiProductController::class, 'update'])->name('products_update')->middleware('Permission:products_update');
    Route::delete('/{id}/delete', [ApiProductController::class, 'destroy'])->name('products_destroy')->middleware('Permission:products_destroy');
});

//order
    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index'])->name('orders_index')->middleware('Permission:orders_index');
        Route::get('/filter', [ApiOrderController::class, 'filter'])->name('orders.filter');
        Route::post('/', [ApiOrderController::class, 'store'])->name('orders_store')->middleware('Permission:orders_store');
        Route::patch('/{id}', [ApiOrderController::class, 'update'])->name('orders_update')->middleware('Permission:orders_update');
        Route::delete('/{id}/delete', [ApiOrderController::class, 'destroy'])->name('orders_destroy')->middleware('Permission:orders_destroy');
    });
//Email
Route::get('/email', [EmailController::class, 'email'])->name('email');
//image
Route::get('/images', [ImageController::class, 'image'])->name('image');



