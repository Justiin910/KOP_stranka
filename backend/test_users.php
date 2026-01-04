<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Test if we can get users
$users = \App\Models\User::with('orders')->get();
echo json_encode($users->map(function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'orders' => $user->orders->count(),
        'totalSpent' => (int)$user->orders->sum('total'),
        'registered' => $user->created_at->format('d.m.Y'),
    ];
})->toArray());
