<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

// Authentication (login/register/logout)
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');

// Email verification
Route::post('verify-email-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1']);

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// Password reset
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest');
Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('guest');
