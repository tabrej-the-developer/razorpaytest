<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AuthController;


// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/', [PaymentController::class, 'checkout']);

Route::get('/create-order', [PaymentController::class, 'createOrder']);
// Route::post('/create-subscription', [PaymentController::class, 'createSubscription'])
//     ->middleware('auth');


    Route::post('/pay-6', [PaymentController::class, 'createSixRupeeOrder']);
Route::post('/verify-6', [PaymentController::class, 'verifySixRupeePayment']);

Route::post('/create-subscription', [PaymentController::class, 'createSubscription']);
Route::post('/razorpay/webhook', [WebhookController::class, 'handle']);



// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');