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

}
