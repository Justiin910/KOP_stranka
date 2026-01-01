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
        // Stricter email validation: prefer RFC + DNS in production; keep RFC in local/testing
        $emailRule = ['required', 'string', 'max:255', 'unique:users,email,' . $request->user()->id];
        if (app()->environment(['local', 'testing'])) {
            $emailRule[] = 'email:rfc';
        } else {
            $emailRule[] = 'email:rfc,dns';
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRule,
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:10'],
        ]);

        // Explicit DNS check for the email domain to avoid bogus domains
        $email = $request->input('email');
        $domain = substr(strrchr($email, '@'), 1);
        if ($domain) {
            $hasMx = function_exists('checkdnsrr') && (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'));
            if (!$hasMx) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Email domain appears invalid or has no DNS records.'],
                ]);
            }
        }

        $user = $request->user();

        // If the email changed, we'll explicitly clear the verified timestamp
        // (it's not mass-assignable on the User model).
        $emailChanged = $user->email !== $validated['email'];

        $user->update($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->save();
        }

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

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Verify password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Nesprávne heslo.'
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'message' => 'Váš účet bol úspešne zmazaný.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Chyba pri mazaní účtu: ' . $e->getMessage()
            ], 500);
        }
    }
}