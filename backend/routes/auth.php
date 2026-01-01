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
// Provide a named GET login route so middleware redirects (route('login')) work
use Illuminate\Http\Request;

Route::get('login', function (Request $request) {
    $url = config('app.frontend_url').'/login';

    // If this request looks like it came from an email verification link
    // (signed link has 'signature' and 'expires'), pass a flag so the
    // frontend can show a "Email verified, please log in" banner.
    if ($request->query('signature') || $request->query('expires') || $request->query('hash')) {
        $url .= (str_contains($url, '?') ? '&' : '?') . 'verified=1';
    }

    return redirect($url);
})->name('login')->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');

// Email verification
Route::post('verify-email-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1']);

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Password reset
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest');
Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('guest');
