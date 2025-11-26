<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Return favorites for authenticated user. Returns product objects.
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([], 200);
        }

        // Get product ids
        $ids = Favorite::where('user_id', $user->id)->pluck('product_id')->toArray();

        // If products table exists, return product objects, otherwise return ids
        try {
            $products = Product::whereIn('id', $ids)->get();
            return response()->json($products, 200);
        } catch (\Throwable $e) {
            return response()->json($ids, 200);
        }
    }

    // Add one or multiple favorites. Accepts product_id or product_ids
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $ids = [];
        if ($request->has('product_ids')) {
            $ids = (array) $request->input('product_ids');
        } elseif ($request->has('product_id')) {
            $ids = [(int) $request->input('product_id')];
        }

        foreach ($ids as $pid) {
            try {
                Favorite::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $pid,
                ]);
            } catch (\Throwable $e) {
                // ignore individual errors
            }
        }

        return response()->json(['success' => true], 201);
    }

    // Remove favorite by product id
    public function destroy(Request $request, $productId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        Favorite::where('user_id', $user->id)->where('product_id', $productId)->delete();

        return response()->json(['success' => true], 200);
    }

    // Replace user's favorites with provided ids
    public function sync(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $ids = (array) $request->input('ids', []);

        // Remove favorites not in new list, and add missing ones
        try {
            \DB::transaction(function () use ($user, $ids) {
                Favorite::where('user_id', $user->id)->whereNotIn('product_id', $ids)->delete();

                foreach ($ids as $pid) {
                    Favorite::firstOrCreate([
                        'user_id' => $user->id,
                        'product_id' => $pid,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Sync failed'], 500);
        }

        return response()->json(['success' => true], 200);
    }
}
