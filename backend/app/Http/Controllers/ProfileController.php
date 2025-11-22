<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:10'],
        ]);

        $user = $request->user();
        
        // Ak sa zmenil email, resetni email verification
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil bol úspešne aktualizovaný.',
            'user' => $user
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Heslo bolo úspešne zmenené.'
        ]);
    }
}