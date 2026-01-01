<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        // If there's no authenticated user (clicked link while logged out),
        // attempt to resolve the user by the route `id` and validate the hash.
        if (is_null($user)) {
            $id = $request->route('id');
            $user = User::find($id);

            if (! $user) {
                abort(403);
            }

            $hash = $request->route('hash');

            if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                abort(403);
            }
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(config('app.frontend_url').'/profile?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(config('app.frontend_url').'/profile?verified=1');
    }
}
