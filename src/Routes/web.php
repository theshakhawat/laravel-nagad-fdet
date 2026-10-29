<?php

use Illuminate\Support\Facades\Route;
use Theshakhawat\NagadFdet\Http\Controllers\NagadPaymentController;

Route::get('/nagad/checkout', [NagadPaymentController::class, 'checkout'])->name('nagad.payment.checkout');
Route::post('/nagad/payment/initiate', [NagadPaymentController::class, 'initiatePayment'])->name('nagad.payment.initiate');
Route::get('/nagad/payment/callback', [NagadPaymentController::class, 'handleCallback'])->name('nagad.payment.callback');
Route::get('/nagad/payment/verify/{paymentRefId?}', [NagadPaymentController::class, 'verifyPayment'])->name('nagad.payment.verify');
