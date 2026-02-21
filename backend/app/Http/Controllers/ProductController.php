<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductReview;
use App\Models\User;

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
        
        // Attach aggregated reviews data if reviews table exists
        if (Schema::hasTable('product_reviews')) {
            $agg = DB::table('product_reviews')
                ->where('product_id', $id)
                ->selectRaw('COUNT(*) as reviews_count, COALESCE(ROUND(AVG(rating),2),0) as avg_rating')
                ->first();

            $reviews = DB::table('product_reviews')
                ->where('product_id', $id)
                ->leftJoin('users', 'product_reviews.user_id', '=', 'users.id')
                ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'users.id as user_id', 'users.name as user_name')
                ->orderByDesc('product_reviews.created_at')
                ->limit(20)
                ->get();

            // If products table has rating/reviews fields, update them for convenience
            try {
                if (Schema::hasColumn('products', 'rating') && Schema::hasColumn('products', 'reviews')) {
                    DB::table('products')->where('id', $id)->update([
                        'rating' => $agg->avg_rating,
                        'reviews' => $agg->reviews_count,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update product rating fields: ' . $e->getMessage());
            }

            return response()->json(array_merge((array)$product, [
                'rating' => (float)($agg->avg_rating ?? 0),
                'reviews' => (int)($agg->reviews_count ?? 0),
                'latest_reviews' => $reviews,
            ]));
        }

        return response()->json($product);
    }

    /**
     * Get or create/update a product review (authenticated users only)
     * One review per user per product - updates if already exists
     */
    public function storeReview(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Ensure product exists
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check if user already has a review for this product
        $existingReview = DB::table('product_reviews')
            ->where('product_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            // Update existing review
            DB::table('product_reviews')
                ->where('id', $existingReview->id)
                ->update([
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? null,
                    'updated_at' => now(),
                ]);
            $reviewId = $existingReview->id;
        } else {
            // Create new review
            $reviewId = DB::table('product_reviews')->insertGetId([
                'product_id' => $id,
                'user_id' => auth()->id(),
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Recalculate aggregates
        $agg = DB::table('product_reviews')
            ->where('product_id', $id)
            ->selectRaw('COUNT(*) as reviews_count, COALESCE(ROUND(AVG(rating),2),0) as avg_rating')
            ->first();

        // Update products table if fields exist
        try {
            if (Schema::hasColumn('products', 'rating') && Schema::hasColumn('products', 'reviews')) {
                DB::table('products')->where('id', $id)->update([
                    'rating' => $agg->avg_rating,
                    'reviews' => $agg->reviews_count,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update product rating fields after review: ' . $e->getMessage());
        }

        $review = DB::table('product_reviews')
            ->leftJoin('users', 'product_reviews.user_id', '=', 'users.id')
            ->where('product_reviews.id', $reviewId)
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name')
            ->first();

        return response()->json([
            'success' => true,
            'review' => $review,
            'rating' => (float)$agg->avg_rating,
            'reviews' => (int)$agg->reviews_count,
        ]);
    }

    /**
     * Get current user's review for a product (if authenticated)
     */
    public function getUserReview($id)
    {
        if (!auth()->check()) {
            return response()->json(['review' => null]);
        }

        $review = DB::table('product_reviews')
            ->leftJoin('users', 'product_reviews.user_id', '=', 'users.id')
            ->where('product_reviews.product_id', $id)
            ->where('product_reviews.user_id', auth()->id())
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name')
            ->first();

        return response()->json(['review' => $review]);
    }

    /**
     * Update user's own review for a product
     */
    public function updateReview(Request $request, $id, $reviewId)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $review = DB::table('product_reviews')
            ->where('id', $reviewId)
            ->where('product_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found or unauthorized'], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Update the review
        DB::table('product_reviews')->where('id', $reviewId)->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'updated_at' => now(),
        ]);

        // Recalculate aggregates
        $agg = DB::table('product_reviews')
            ->where('product_id', $id)
            ->selectRaw('COUNT(*) as reviews_count, COALESCE(ROUND(AVG(rating),2),0) as avg_rating')
            ->first();

        // Update products table if fields exist
        try {
            if (Schema::hasColumn('products', 'rating') && Schema::hasColumn('products', 'reviews')) {
                DB::table('products')->where('id', $id)->update([
                    'rating' => $agg->avg_rating,
                    'reviews' => $agg->reviews_count,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update product rating fields after review update: ' . $e->getMessage());
        }

        $updatedReview = DB::table('product_reviews')
            ->leftJoin('users', 'product_reviews.user_id', '=', 'users.id')
            ->where('product_reviews.id', $reviewId)
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name')
            ->first();

        return response()->json([
            'success' => true,
            'review' => $updatedReview,
            'rating' => (float)$agg->avg_rating,
            'reviews' => (int)$agg->reviews_count,
        ]);
    }

    /**
         * Delete a review - Users can delete their own, owners/admins can delete any
     */
    public function deleteReview($id, $reviewId)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $isAdmin = $user->role === 'admin' || $user->role === 'owner';

        $review = DB::table('product_reviews')
            ->where('id', $reviewId)
            ->where('product_id', $id)
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        // Check authorization: user can delete own review, or admin/owner can delete any
        if ($review->user_id !== auth()->id() && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized to delete this review'], 403);
        }

        // Delete the review
        DB::table('product_reviews')->where('id', $reviewId)->delete();

        // Recalculate aggregates
        $agg = DB::table('product_reviews')
            ->where('product_id', $id)
            ->selectRaw('COUNT(*) as reviews_count, COALESCE(ROUND(AVG(rating),2),0) as avg_rating')
            ->first();

        // Update products table if fields exist
        try {
            if (Schema::hasColumn('products', 'rating') && Schema::hasColumn('products', 'reviews')) {
                DB::table('products')->where('id', $id)->update([
                    'rating' => $agg->avg_rating,
                    'reviews' => $agg->reviews_count,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update product rating fields after review deletion: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
            'rating' => (float)$agg->avg_rating,
            'reviews' => (int)$agg->reviews_count,
        ]);
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
