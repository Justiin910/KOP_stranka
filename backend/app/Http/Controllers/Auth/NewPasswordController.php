<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Require RFC + DNS validation for the provided email
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Extra DNS check: ensure the domain has MX or A records
        $email = $request->input('email');
        $domain = substr(strrchr($email, '@'), 1);
        if ($domain) {
            $hasMx = function_exists('checkdnsrr') && (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'));
            if (!$hasMx) {
                throw ValidationException::withMessages([
                    'email' => ['Email domain appears invalid or has no DNS records.'],
                ]);
            }
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $newPassword = $request->string('password');

                // 🔒 kontrola: nové heslo nesmie byť rovnaké ako staré
                if (Hash::check($newPassword, $user->password)) {
                    throw ValidationException::withMessages([
                        'password' => ['Nové heslo nesmie byť rovnaké ako aktuálne heslo.'],
                    ]);
                }

                $user->forceFill([
                    'password' => Hash::make($newPassword),
                    'remember_token' => Str::random(60),
                ])->save();

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
                    // Don't fail the password reset if email fails, just log it
                }

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}
