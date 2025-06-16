<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {

    Route::middleware('auth:sanctum', 'is_verified')->group(function () {

        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

        Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');

        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});
