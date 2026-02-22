<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'message' => __('messages.profile.profile_updated'),
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

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Send password change notification email
        try {
            $changeTime = now()->format('d.m.Y H:i');
            $htmlBody = view('emails.password-changed', [
                'user' => $user,
                'changeTime' => $changeTime
            ])->render();

            Mail::html($htmlBody, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Vaše heslo bolo zmenené')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info("Password change notification email sent to user {$user->id} ({$user->email})");
        } catch (\Exception $e) {
            Log::error("Failed to send password change notification email for user {$user->id}: " . $e->getMessage());
            // Don't fail the password update if email fails, just log it
        }

        return response()->json([
            'message' => __('messages.profile.password_changed')
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
                'message' => __('messages.profile.incorrect_password')
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'message' => __('messages.profile.account_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.profile.account_delete_error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $validated = $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
            ]);

            $user = $request->user();

            // Delete old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Ensure avatars directory exists
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars', 0755, true);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            // Update user record
            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => __('messages.profile.avatar_updated'),
                'user' => $user,
                'avatar_url' => Storage::disk('public')->url($path)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => __('messages.profile.validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => __('messages.profile.avatar_upload_error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    /**
     * Update user language preference
     */
    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:sk,en,de,fr,es,it,pl,cs'],
        ]);

        $user = $request->user();
        $user->update(['language' => $validated['language']]);
        
        // Refresh from database to ensure the update worked
        $user->refresh();

        return response()->json([
            'message' => 'Language preference updated',
            'language' => $user->language,
            'user' => $user,
        ]);
    }
}