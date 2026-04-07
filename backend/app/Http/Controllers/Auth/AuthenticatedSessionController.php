<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        if ($user->two_factor_enabled && $user->email_verified_at) {
            $this->sendTwoFactorCode($user, 'login', $request->boolean('resend_2fa'));

            return response()->json([
                'requires_2fa' => true,
                'message' => __('messages.auth.two_factor_code_sent'),
            ]);
        }
        
        return $this->createTokenResponse($user);
    }

    public function verifyTwoFactor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !$user->two_factor_enabled || !$user->email_verified_at) {
            return response()->json([
                'message' => __('messages.auth.two_factor_invalid_context'),
            ], 422);
        }

        $isExpired = !$user->two_factor_expires_at || Carbon::parse($user->two_factor_expires_at)->isPast();
        $isValid = $user->two_factor_code && Hash::check($validated['code'], $user->two_factor_code);

        if ($isExpired || !$isValid || $user->two_factor_purpose !== 'login') {
            return response()->json([
                'message' => __('messages.auth.two_factor_invalid_code'),
            ], 422);
        }

        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
            'two_factor_purpose' => null,
        ])->save();
        
        return $this->createTokenResponse($user);
    }

    private function createTokenResponse(User $user): JsonResponse
    {
        $token = $user->createToken('api-token');

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'language' => $user->language,
                'email_verified_at' => $user->email_verified_at,
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
            ],
        ]);
    }

    private function sendTwoFactorCode(User $user, string $purpose, bool $forceResend = false): void
    {
        $hasActiveCode =
            $user->two_factor_code &&
            $user->two_factor_purpose === $purpose &&
            $user->two_factor_expires_at &&
            Carbon::parse($user->two_factor_expires_at)->isFuture();

        if ($hasActiveCode && !$forceResend) {
            return;
        }

        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(10),
            'two_factor_purpose' => $purpose,
        ])->save();

        Mail::to($user->email)
            ->locale(app()->getLocale())
            ->send(new VerificationCodeMail($code, 10, $purpose));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Revoke the current token by deleting it
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => __('messages.auth.logged_out')]);
    }
}
