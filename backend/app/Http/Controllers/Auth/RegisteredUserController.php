<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        // Always use RFC + DNS validation for emails (reject clearly bogus domains)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'phone' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Extra safeguard: explicit DNS check for domain (MX or A record)
        $email = $request->string('email');
        $domain = substr(strrchr($email, '@'), 1);
        if ($domain) {
            $hasMx = function_exists('checkdnsrr') && (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'));
            if (!$hasMx) {
                throw ValidationException::withMessages([
                    'email' => ['Email domain appears invalid or has no DNS records.'],
                ]);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            "phone" => $request->phone,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

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
