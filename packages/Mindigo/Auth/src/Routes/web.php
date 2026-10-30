<?php

use Illuminate\Support\Facades\Route;
use Mindigo\Auth\Http\Controllers\ForgotPasswordController;
use Mindigo\Auth\Http\Controllers\LoginController;
use Mindigo\Auth\Http\Controllers\RegistrationController;

Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegistrationController::class, 'index'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');
    Route::get('/register/complete', [RegistrationController::class, 'complete'])->name('register.complete');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
    Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');

});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware(['web', 'auth']);
