<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerificationCodeController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::post('register', [RegisteredUserController::class, 'store'])->name('register');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1', 'is_json')->name('verification.send'); // resend

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

        Route::post('/send-code', [VerificationCodeController::class, 'sendCode'])->name('send.code');

        Route::post('/verify-code', [VerificationCodeController::class, 'verify'])->middleware('throttle:6,1')->name('verify.code');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');

        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

        Route::post('/password/verify-otp', [NewPasswordController::class, 'verify']);

        Route::post('/password/reset', [NewPasswordController::class, 'resetPassword'])->name('password.reset');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
