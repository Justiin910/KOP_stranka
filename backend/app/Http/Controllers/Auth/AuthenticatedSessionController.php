<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();
        
        // Create API token with expiration based on remember flag
        $expiresIn = $request->boolean('remember') ? 30 * 24 * 60 : 2 * 60; // 30 days or 2 hours in minutes
        $token = $user->createToken(
            'mobile-app',
            ['*'],
            now()->addMinutes($expiresIn)
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
            'remember' => $request->boolean('remember'),
        ]);
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

        return response()->json(['message' => 'Logged out successfully']);
    }
}
