<?php

use Illuminate\Support\Facades\Route;
use Webkul\Paypal\Http\Controllers\MpesaController;
use Webkul\Paypal\Http\Controllers\StandardController;
use Webkul\Paypal\Http\Controllers\SmartButtonController;
use Webkul\Shop\Http\Controllers\CartController; // Adjust the namespace as needed

Route::group(['middleware' => ['web']], function () {
    Route::prefix('paypal/standard')->group(function () {
        Route::get('/redirect', [StandardController::class, 'redirect'])->name('paypal.standard.redirect');
        Route::get('/success', [StandardController::class, 'success'])->name('paypal.standard.success');
        Route::get('/cancel', [StandardController::class, 'cancel'])->name('paypal.standard.cancel');
    });

    Route::prefix('paypal/smart-button')->group(function () {
        Route::get('/create-order', [SmartButtonController::class, 'createOrder'])->name('paypal.smart-button.create-order');
        Route::post('/capture-order', [SmartButtonController::class, 'captureOrder'])->name('paypal.smart-button.capture-order');
    });

    Route::prefix('paypal/mpesa')->group(function () {
        Route::match(['get', 'post'], '/redirect', [MpesaController::class, 'redirect'])->name('mpesa.redirect');
        Route::get('/success', [MpesaController::class, 'success'])->name('mpesa.success');
        Route::get('/cancel', [MpesaController::class, 'cancel'])->name('mpesa.cancel');
    });

    Route::post('paypal/standard/ipn', [StandardController::class, 'ipn'])
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->name('paypal.standard.ipn');

    Route::post('paypal/mpesa/ipn', [MpesaController::class, 'ipn'])
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->name('mpesa.ipn');

    // Define the cart route
    Route::get('/checkout/cart', [CartController::class, 'view'])->name('shop.checkout.cart');
});
