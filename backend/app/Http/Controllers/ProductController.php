<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Get a single product by ID.
     */
    public function show($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    public function byCategory($category)
    {
        $products = DB::table('products')->where('category', $category)->get();
        return response()->json($products);
    }

    /**
     * Search products by query string across title and description.
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            // If no query provided, return empty array to avoid returning all products
            return response()->json([]);
        }

        try {
            // Build a safe query that doesn't reference columns which may not exist
            $productsQuery = DB::table('products')
                ->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->orWhere('brand', 'like', "%{$q}%")
                ->orWhere('category', 'like', "%{$q}%");

            // Include slug search only when the column exists (some installs don't have it)
            if (Schema::hasColumn('products', 'slug')) {
                $productsQuery = $productsQuery->orWhere('slug', 'like', "%{$q}%");
            }

            $products = $productsQuery->limit(100)->get();
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Product search failed: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Return products currently on sale (discount >= requested threshold)
     */
    public function sale(Request $request)
    {
        $minDiscount = (int) $request->query('min_discount', 10);
        $limit = (int) $request->query('limit', 12);

        try {
            // Prefer records that have explicit discount fields
            $query = DB::table('products')
                ->where(function($q) use ($minDiscount) {
                    // If discount_value column exists, use it
                    $q->whereRaw("COALESCE(discount_value, 0) >= ?", [$minDiscount])
                      // Or fallback to oldPrice/current price calculation
                      ->orWhereRaw("CASE WHEN oldPrice IS NOT NULL AND oldPrice > 0 THEN ROUND((oldPrice - price) / oldPrice * 100) >= ? ELSE 0 END", [$minDiscount]);
                })
                ->orderByDesc('discount_value')
                ->limit($limit);

            $products = $query->get();
            return response()->json($products);
        } catch (\Exception $e) {
            \Log::error('Failed to load sale products: ' . $e->getMessage());
            return response()->json([]);
        }
    }

}
