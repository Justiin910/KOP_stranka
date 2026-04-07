<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerifyEmailController;

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

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
