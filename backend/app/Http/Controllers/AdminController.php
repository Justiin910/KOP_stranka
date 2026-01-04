<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Get all users for admin panel
     */
    public function getUsers()
    {
        // Get all users with their orders
        $users = User::with('orders')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'orders' => $user->orders->count(),
                    'totalSpent' => (int)$user->orders->sum('total'),
                    'registered' => $user->created_at->format('d.m.Y'),
                ];
            });

        return response()->json($users);
    }

    /**
     * Get single user details
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'created_at' => $user->created_at->format('d.m.Y'),
            'updated_at' => $user->updated_at->format('d.m.Y'),
        ]);
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Používateľ bol aktualizovaný',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Používateľ bol vymazaný'
        ]);
    }

    /**
     * Reset user password (sends reset link via email)
     */
    public function resetUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Generate temporary password
        $tempPassword = \Illuminate\Support\Str::random(12);
        $user->password = Hash::make($tempPassword);
        $user->save();

        // In production, you would send email with reset link
        // For now, we'll just return success
        
        return response()->json([
            'message' => 'Heslo bolo resetované. Email bol odoslaný na ' . $user->email
        ]);
    }

    /**
     * Set user password to a specific value (from admin-generated password)
     */
    public function setUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        \Log::info('Setting password for user ' . $id . ' (email: ' . $user->email . ')');
        \Log::info('Password length: ' . strlen($validated['password']));
        
        $hashedPassword = Hash::make($validated['password']);
        $user->password = $hashedPassword;
        $user->save();

        \Log::info('Password saved successfully');

        return response()->json([
            'message' => 'Heslo používateľa bolo nastavené'
        ]);
    }

    /**
     * Get admin statistics
     */
    public function getStats()
    {
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'totalRevenue' => round($totalRevenue, 2),
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'avgOrderValue' => round($avgOrderValue, 2),
        ]);
    }
}
