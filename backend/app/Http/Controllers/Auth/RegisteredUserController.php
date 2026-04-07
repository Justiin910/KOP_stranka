<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate email with RFC + DNS rules
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'phone' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Additional MX check for email domain
        $email = $request->string('email');
        $domain = substr(strrchr($email, '@'), 1);
        if ($domain) {
            $hasMx = function_exists('checkdnsrr') && checkdnsrr($domain, 'MX');
            if (!$hasMx) {
                throw ValidationException::withMessages([
                    'email' => [__('messages.auth.invalid_domain')],
                ]);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            "phone" => $request->phone,
            'password' => Hash::make($request->string('password')),
        ]);

        // Send sale/promotion notification (created first)
        Notification::create([
            'user_id' => $user->id,
            'type' => 'promotion',
            'title' => 'Výpredaj! 🎉',
            'message' => 'Zľavy na vybrané položky až -30%! Pospešujte, ponuka platí len do vypredania!',
            'read' => false,
        ]);

        // Send welcome notification (created second, so it appears first in DESC order)
        Notification::create([
            'user_id' => $user->id,
            'type' => 'general',
            'title' => 'Vítame vás!',
            'message' => 'Ďakujeme za registráciu. Teší nás, že ste sa nám pripojili!',
            'read' => false,
        ]);

        event(new Registered($user));

        // Generate token with 30-day expiration for new users
        $token = $user->createToken(
            'mobile-app',
            ['*'],
            now()->addDays(30)
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }
}
