<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\PaymentCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $forceResendEmailChangeCode = $request->boolean('resend_email_change_2fa');

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

        $emailChanged = $user->email !== $validated['email'];

        if ($emailChanged && $user->two_factor_enabled && $user->email_verified_at) {
            $hasActiveCode =
                $user->two_factor_code &&
                $user->two_factor_purpose === 'email_change' &&
                $user->two_factor_pending_email === $validated['email'] &&
                $user->two_factor_expires_at &&
                $user->two_factor_expires_at->isFuture();

            if (!$hasActiveCode || $forceResendEmailChangeCode) {
                $code = (string) random_int(100000, 999999);

                $user->forceFill([
                    'two_factor_code' => Hash::make($code),
                    'two_factor_expires_at' => now()->addMinutes(10),
                    'two_factor_purpose' => 'email_change',
                    'two_factor_pending_email' => $validated['email'],
                ])->save();

                Mail::to($user->email)
                    ->locale(app()->getLocale())
                    ->send(new VerificationCodeMail($code, 10, 'email_change'));
            }

            return response()->json([
                'message' => __('messages.auth.email_change_code_sent'),
                'requires_email_change_2fa' => true,
                'pending_email' => $validated['email'],
            ], 202);
        }

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
     * Enable or disable email-based two-factor authentication.
     */
    public function updateTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $beforeEnabled = (bool) ($user->two_factor_enabled ?? false);

        if ($validated['enabled'] && !$user->email_verified_at) {
            return response()->json([
                'message' => __('messages.auth.two_factor_requires_verified_email'),
            ], 422);
        }

        $user->forceFill([
            'two_factor_enabled' => $validated['enabled'],
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
            'two_factor_purpose' => null,
            'two_factor_pending_email' => null,
        ])->save();

        $afterEnabled = (bool) ($user->two_factor_enabled ?? false);
        if ($beforeEnabled !== $afterEnabled) {
            $this->sendTwoFactorStatusEmail($user, $afterEnabled);
        }

        return response()->json([
            'message' => $validated['enabled']
                ? __('messages.auth.two_factor_enabled')
                : __('messages.auth.two_factor_disabled'),
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ]);
    }

    /**
     * Confirm pending email change with 2FA code.
     */
    public function confirmEmailChange(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        $isExpired = !$user->two_factor_expires_at || $user->two_factor_expires_at->isPast();
        $isValid = $user->two_factor_code && Hash::check($validated['code'], $user->two_factor_code);
        $hasPendingEmail = !empty($user->two_factor_pending_email);

        if (
            $user->two_factor_purpose !== 'email_change' ||
            !$hasPendingEmail ||
            $isExpired ||
            !$isValid
        ) {
            return response()->json([
                'message' => __('messages.auth.two_factor_invalid_code'),
            ], 422);
        }

        $user->email = $user->two_factor_pending_email;
        $user->email_verified_at = null;
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->two_factor_purpose = null;
        $user->two_factor_pending_email = null;
        $user->save();

        return response()->json([
            'message' => __('messages.auth.email_changed_successfully'),
            'user' => $user,
        ]);
    }

    /**
     * Get default saved payment card for authenticated user.
     */
    public function getPaymentCard(Request $request)
    {
        $card = $request->user()->paymentCards()->where('is_default', true)->latest()->first();

        return response()->json([
            'card' => $card ? $card->toPublicArray(true) : null,
        ]);
    }

    /**
     * Save or update default payment card for authenticated user.
     */
    public function savePaymentCard(Request $request)
    {
        $validated = $request->validate([
            'cardholder_name' => ['required', 'string', 'max:120'],
            'card_number' => ['required', 'string'],
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/'],
        ]);

        $digits = preg_replace('/\D+/', '', $validated['card_number']);
        if (strlen($digits) < 13 || strlen($digits) > 19) {
            return response()->json([
                'message' => __('messages.order.invalid_card_number'),
            ], 422);
        }

        [$monthRaw, $yearRaw] = explode('/', $validated['expiry']);
        $expiryMonth = (int) $monthRaw;
        $expiryYear = 2000 + (int) $yearRaw;

        $user = $request->user();
        $brand = $this->detectCardBrand($digits);
        $last4 = substr($digits, -4);

        PaymentCard::where('user_id', $user->id)->update(['is_default' => false]);

        $card = PaymentCard::create([
            'user_id' => $user->id,
            'cardholder_name' => trim($validated['cardholder_name']),
            'card_number' => $digits,
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'brand' => $brand,
            'last4' => $last4,
            'is_default' => true,
        ]);

        return response()->json([
            'message' => __('messages.order.card_saved_successfully'),
            'card' => $card->toPublicArray(true),
        ], 201);
    }

    /**
     * Delete default payment card for authenticated user.
     */
    public function deletePaymentCard(Request $request)
    {
        $request->user()->paymentCards()->where('is_default', true)->delete();

        return response()->json([
            'message' => __('messages.order.card_deleted_successfully'),
        ]);
    }

    private function detectCardBrand(string $digits): string
    {
        if (preg_match('/^4/', $digits)) {
            return 'visa';
        }
        if (preg_match('/^(5[1-5]|2[2-7])/', $digits)) {
            return 'mastercard';
        }
        if (preg_match('/^3[47]/', $digits)) {
            return 'amex';
        }

        return 'card';
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
                        ->subject(__('messages.email.password_changed.subject'))
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

        $email = trim((string) ($user->email ?? ''));
        $userName = (string) ($user->name ?? '');
        $locale = $this->resolveEmailLocale($user->language ?? null);

        try {
            $user->delete();

            if ($email !== '') {
                $this->sendAccountDeletedEmail($email, $userName, $locale);
            }

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
                'crop_x' => ['nullable', 'integer', 'min:0'],
                'crop_y' => ['nullable', 'integer', 'min:0'],
                'crop_width' => ['nullable', 'integer', 'min:1'],
                'crop_height' => ['nullable', 'integer', 'min:1'],
            ]);

            $user = $request->user();
            $avatarFile = $request->file('avatar');
            $isGif = strtolower((string) $avatarFile->getClientOriginalExtension()) === 'gif';
            $hasCrop = $request->filled(['crop_x', 'crop_y', 'crop_width', 'crop_height']);

            // Delete old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Ensure avatars directory exists
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars');
            }

            // Store new avatar (with optional GIF crop for animated images)
            if ($isGif && $hasCrop) {
                if (!class_exists('Imagick')) {
                    return response()->json([
                        'message' => __('messages.profile.avatar_gif_crop_unavailable')
                    ], 422);
                }

                $cropX = (int) $validated['crop_x'];
                $cropY = (int) $validated['crop_y'];
                $cropWidth = (int) $validated['crop_width'];
                $cropHeight = (int) $validated['crop_height'];

                $gif = new \Imagick();
                $gif->readImage($avatarFile->getRealPath());
                $gif = $gif->coalesceImages();

                $processed = new \Imagick();
                foreach ($gif as $frame) {
                    $frameClone = clone $frame;

                    $frameWidth = $frameClone->getImageWidth();
                    $frameHeight = $frameClone->getImageHeight();

                    $safeWidth = max(1, min($cropWidth, $frameWidth));
                    $safeHeight = max(1, min($cropHeight, $frameHeight));
                    $safeX = max(0, min($cropX, max(0, $frameWidth - $safeWidth)));
                    $safeY = max(0, min($cropY, max(0, $frameHeight - $safeHeight)));

                    $frameClone->cropImage($safeWidth, $safeHeight, $safeX, $safeY);
                    $frameClone->setImagePage(0, 0, 0, 0);
                    $frameClone->resizeImage(512, 512, \Imagick::FILTER_LANCZOS, 1, false);
                    $frameClone->setImageFormat('gif');
                    $frameClone->setImageDelay($frame->getImageDelay());
                    $frameClone->setImageDispose($frame->getImageDispose());

                    $processed->addImage($frameClone);
                    $processed->setImageDelay($frame->getImageDelay());
                }

                $processed = $processed->deconstructImages();
                $path = 'avatars/' . uniqid('avatar_', true) . '.gif';
                Storage::disk('public')->put($path, $processed->getImagesBlob());

                $gif->clear();
                $gif->destroy();
                $processed->clear();
                $processed->destroy();
            } else {
                $path = $avatarFile->store('avatars', 'public');
            }

            // Update user record
            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => __('messages.profile.avatar_updated'),
                'user' => $user,
                'avatar_url' => Storage::url($path)
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
     * Reset user avatar to default (initials/placeholder on frontend)
     */
    public function resetAvatar(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update(['avatar' => null]);

            return response()->json([
                'message' => __('messages.profile.avatar_reset_success'),
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Avatar reset error: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => __('messages.profile.avatar_reset_error'),
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

    private function resolveEmailLocale(?string $preferredLocale): string
    {
        $fallback = app()->getLocale();
        $preferred = is_string($preferredLocale) ? strtolower(trim($preferredLocale)) : '';

        if (in_array($preferred, ['sk', 'en'], true)) {
            return $preferred;
        }

        return in_array($fallback, ['sk', 'en'], true) ? $fallback : 'sk';
    }

    private function runWithEmailLocale(string $locale, callable $callback): void
    {
        $currentLocale = app()->getLocale();
        app()->setLocale($locale);

        try {
            $callback();
        } finally {
            app()->setLocale($currentLocale);
        }
    }

    private function sendAccountDeletedEmail(string $email, string $userName, string $locale): void
    {
        try {
            $this->runWithEmailLocale($locale, function () use ($email, $userName) {
                Mail::send('emails.account-deleted', [
                    'userName' => $userName,
                    'deletedAt' => now()->format('d.m.Y H:i'),
                ], function ($message) use ($email) {
                    $message->to($email)
                        ->subject(__('messages.email.account_deleted.subject'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send account-deleted email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTwoFactorStatusEmail(User $user, bool $enabled): void
    {
        $email = trim((string) ($user->email ?? ''));
        if ($email === '') {
            return;
        }

        $throttleKey = 'email:two-factor-status:' . (int) $user->id;
        $maxAttempts = 1;
        $cooldownSeconds = 300;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            Log::info('Two-factor status email skipped due to cooldown', [
                'user_id' => $user->id,
                'enabled' => $enabled,
                'available_in' => RateLimiter::availableIn($throttleKey),
            ]);
            return;
        }

        RateLimiter::hit($throttleKey, $cooldownSeconds);

        $locale = $this->resolveEmailLocale($user->language ?? null);

        try {
            $this->runWithEmailLocale($locale, function () use ($email, $user, $enabled) {
                Mail::send('emails.two-factor-status', [
                    'userName' => (string) ($user->name ?? ''),
                    'enabled' => $enabled,
                    'changedAt' => now()->format('d.m.Y H:i'),
                ], function ($message) use ($email, $enabled) {
                    $subjectKey = $enabled
                        ? 'messages.email.two_factor_status.subject_enabled'
                        : 'messages.email.two_factor_status.subject_disabled';

                    $message->to($email)
                        ->subject(__($subjectKey))
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send two-factor status email', [
                'user_id' => $user->id,
                'email' => $email,
                'enabled' => $enabled,
                'error' => $e->getMessage(),
            ]);
        }
    }
}