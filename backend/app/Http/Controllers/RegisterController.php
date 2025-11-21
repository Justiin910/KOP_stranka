<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class RegisterController extends Controller
{
    public function startRegistration(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email|unique:pending_users,email",
            "phone" => "required",
            "password" => "required|min:8",
        ]);

        $code = rand(100000, 999999);

        DB::table('pending_users')->insert([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password),
            "verification_code" => $code,
            "created_at" => now(),
        ]);
        

        Mail::raw("Váš overovací kód je: $code", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject("Overovací kód");
        });

        return response()->json(["status" => "sent"]);
    }

    public function removePending(Request $request)
    {
        $request->validate([
            "email" => "required|email"
        ]);

        DB::table('pending_users')->where('email', $request->email)->delete();

        return response()->json(["status" => "removed"]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "code"  => "required"
        ]);

        $pending = DB::table('pending_users')
            ->where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();

        if (!$pending) {
            return response()->json(["error" => "Invalid code"], 400);
        }

        $userId = DB::table('users')->insertGetId([
            "name" => $pending->name,
            "email" => $pending->email,
            "phone" => $pending->phone,
            "password" => $pending->password,
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        DB::table('pending_users')->where('email', $pending->email)->delete();

        return response()->json(["status" => "verified"]);
    }

    public function resend(Request $request)
    {
        $pending = DB::table('pending_users')->where('email', $request->email)->first();

        if (!$pending) {
            return response()->json(["error" => "Not found"], 404);
        }

        $code = rand(100000, 999999);

        DB::table('pending_users')->where('email', $request->email)
            ->update(["verification_code" => $code]);

        Mail::raw("Váš nový overovací kód: $code", function ($msg) use ($pending) {
            $msg->to($pending->email)->subject("Nový overovací kód");
        });

        return response()->json(["status" => "resent"]);
    }
}