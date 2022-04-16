<?php

use App\Helper\ResponseHelper;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\SizeController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('not-auth', [AuthController::class, 'notAuth'])->name('notAuth');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')
        ->middleware('auth:sanctum');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('categories/select-category', [CategoryController::class, 'selectCategory'])
        ->name('categories.selectCategory');
    Route::put('categories/{category}/change-status', [CategoryController::class, 'changeStatus'])
        ->name('categories.changeStatus')->missing(function () {
            return \App\Helper\ResponseHelper::notFound();
        });
    Route::put('products/{product}/change-status', [ProductController::class, 'changeStatus'])
        ->name('products.changeStatus')->missing(function () {
            return \App\Helper\ResponseHelper::notFound();
        });
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])
        ->name('products.images.destroy')->missing(function () {
            return ResponseHelper::notFound();
        })->scopeBindings();
    Route::put('products/{product}/sizes/{size}/change-status', [SizeController::class, 'changeStatus'])
        ->name('products.sizes.changeStatus')->missing(function () {
            return ResponseHelper::notFound();
        })->scopeBindings();

    Route::apiResource('categories', CategoryController::class)->missing(function () {
        return ResponseHelper::notFound();
    });
    Route::apiResource('products', ProductController::class)->missing(function () {
        return ResponseHelper::notFound();
    });
    Route::apiResource('products.sizes', SizeController::class)
        ->missing(function () {
            return ResponseHelper::notFound();
        })->except('show');
});
