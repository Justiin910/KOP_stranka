<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Notification;

class ProductController extends Controller
{
    /**
     * Get a single product by ID.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Attach aggregated reviews data if reviews table exists
        if (Schema::hasTable('product_reviews')) {
            $agg = DB::table('product_reviews')
                ->where('product_id', $id)
                ->selectRaw('COUNT(*) as reviews_count, COALESCE(ROUND(AVG(rating),2),0) as avg_rating')
                ->first();

            $reviews = DB::table('product_reviews')
                ->where('product_id', $id)
                ->leftJoin('users', 'product_reviews.user_id', '=', 'users.id')
                ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar')
                ->orderByDesc('product_reviews.created_at')
                ->limit(20)
                ->get();

            $reviews = $this->attachCommentsToReviews($reviews);

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

            return response()->json(array_merge($product->toArray(), [
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
            return response()->json(['message' => __('messages.product.unauthorized')], 401);
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Ensure product exists
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['message' => __('messages.product.not_found')], 404);
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
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar')
            ->first();

        $review = $this->attachCommentsToReviews(collect([$review]))->first();

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
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar')
            ->first();

        if ($review) {
            $review = $this->attachCommentsToReviews(collect([$review]))->first();
        }

        return response()->json(['review' => $review]);
    }

    /**
     * Update user's own review for a product
     */
    public function updateReview(Request $request, $id, $reviewId)
    {
        if (!auth()->check()) {
            return response()->json(['message' => __('messages.product.unauthorized')], 401);
        }

        $review = DB::table('product_reviews')
            ->where('id', $reviewId)
            ->where('product_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$review) {
            return response()->json(['message' => __('messages.product.review_not_found')], 404);
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
            ->select('product_reviews.id', 'product_reviews.rating', 'product_reviews.comment', 'product_reviews.created_at', 'product_reviews.updated_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar')
            ->first();

        $updatedReview = $this->attachCommentsToReviews(collect([$updatedReview]))->first();

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
            return response()->json(['message' => __('messages.product.unauthorized')], 401);
        }

        $user = auth()->user();
        $isAdmin = $user->role === 'admin' || $user->role === 'owner';

        $review = DB::table('product_reviews')
            ->where('id', $reviewId)
            ->where('product_id', $id)
            ->first();

        if (!$review) {
            return response()->json(['message' => __('messages.product.review_not_found_delete')], 404);
        }

        // Check authorization: user can delete own review, or admin/owner can delete any
        if ((int) $review->user_id !== (int) auth()->id() && !$isAdmin) {
            return response()->json(['message' => __('messages.product.review_unauthorized_delete')], 403);
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
            'message' => __('messages.product.review_deleted'),
            'rating' => (float)$agg->avg_rating,
            'reviews' => (int)$agg->reviews_count,
        ]);
    }

    /**
     * Create a comment on review or a reply to an existing review comment.
     */
    public function storeReviewComment(Request $request, $id, $reviewId)
    {
        if (!auth()->check()) {
            return response()->json(['message' => __('messages.product.unauthorized')], 401);
        }

        if (!Schema::hasTable('product_review_comments')) {
            return response()->json(['message' => 'Review comments are not available yet.'], 503);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer',
        ]);

        $review = DB::table('product_reviews')
            ->where('id', $reviewId)
            ->where('product_id', $id)
            ->first();

        if (!$review) {
            return response()->json(['message' => __('messages.product.review_not_found')], 404);
        }

        $parentId = $validated['parent_id'] ?? null;
        $parentComment = null;
        if ($parentId !== null) {
            $parentComment = DB::table('product_review_comments')
                ->where('id', $parentId)
                ->where('review_id', $reviewId)
                ->first();

            if (!$parentComment) {
                return response()->json(['message' => 'Parent comment not found.'], 422);
            }
        }

        $commentId = DB::table('product_review_comments')->insertGetId([
            'review_id' => $reviewId,
            'user_id' => auth()->id(),
            'parent_id' => $parentId,
            'comment' => trim((string) $validated['comment']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $actorId = (int) auth()->id();
        $recipientId = null;
        $notificationTitle = null;
        $notificationMessage = null;

        $authorName = auth()->user()?->name ?: 'Používateľ';
        $productTitle = (string) (DB::table('products')->where('id', $id)->value('title') ?? "#{$id}");
        $commentPreview = Str::limit(trim((string) $validated['comment']), 180);

        // 1) Reply to an existing discussion comment -> notify that comment author.
        if (
            $parentComment &&
            !empty($parentComment->user_id) &&
            (int) $parentComment->user_id !== $actorId
        ) {
            $recipientId = (int) $parentComment->user_id;
            $notificationTitle = 'Niekto odpovedal na váš komentár';
            $notificationMessage = "{$authorName} odpovedal na váš komentár pri produkte {$productTitle}: \"{$commentPreview}\"";
        }

        // 2) New top-level discussion comment under a review -> notify review author.
        if (
            !$recipientId &&
            !empty($review->user_id) &&
            (int) $review->user_id !== $actorId
        ) {
            $recipientId = (int) $review->user_id;
            $notificationTitle = 'Niekto komentoval vašu recenziu';
            $notificationMessage = "{$authorName} pridal komentár k vašej recenzii pri produkte {$productTitle}: \"{$commentPreview}\"";
        }

        if ($recipientId && $notificationTitle && $notificationMessage) {
            try {
                Notification::create([
                    'user_id' => $recipientId,
                    'type' => 'general',
                    'title' => $notificationTitle,
                    'message' => $notificationMessage,
                    'read' => false,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create review discussion notification: ' . $e->getMessage(), [
                    'review_id' => $reviewId,
                    'recipient_id' => $recipientId,
                    'actor_id' => $actorId,
                ]);
            }
        }

        $comment = DB::table('product_review_comments')
            ->leftJoin('users', 'product_review_comments.user_id', '=', 'users.id')
            ->where('product_review_comments.id', $commentId)
            ->select(
                'product_review_comments.id',
                'product_review_comments.review_id',
                'product_review_comments.parent_id',
                'product_review_comments.comment',
                'product_review_comments.created_at',
                'product_review_comments.updated_at',
                'users.id as user_id',
                'users.name as user_name',
                'users.avatar as user_avatar'
            )
            ->first();

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => (int) $comment->id,
                'review_id' => (int) $comment->review_id,
                'parent_id' => $comment->parent_id !== null ? (int) $comment->parent_id : null,
                'comment' => (string) $comment->comment,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'user_id' => $comment->user_id !== null ? (int) $comment->user_id : null,
                'user_name' => $comment->user_name,
                'user_avatar' => $comment->user_avatar,
                'replies' => [],
            ],
        ]);
    }

    private function attachCommentsToReviews(Collection $reviews): Collection
    {
        if ($reviews->isEmpty()) {
            return $reviews;
        }

        foreach ($reviews as $review) {
            if ($review) {
                $review->comments = [];
            }
        }

        if (!Schema::hasTable('product_review_comments')) {
            return $reviews;
        }

        $reviewIds = $reviews->pluck('id')->filter()->values()->all();
        if (empty($reviewIds)) {
            return $reviews;
        }

        $commentRows = DB::table('product_review_comments')
            ->leftJoin('users', 'product_review_comments.user_id', '=', 'users.id')
            ->whereIn('product_review_comments.review_id', $reviewIds)
            ->select(
                'product_review_comments.id',
                'product_review_comments.review_id',
                'product_review_comments.parent_id',
                'product_review_comments.comment',
                'product_review_comments.created_at',
                'product_review_comments.updated_at',
                'users.id as user_id',
                'users.name as user_name',
                'users.avatar as user_avatar'
            )
            ->orderBy('product_review_comments.created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'review_id' => (int) $row->review_id,
                    'parent_id' => $row->parent_id !== null ? (int) $row->parent_id : null,
                    'comment' => (string) $row->comment,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'user_id' => $row->user_id !== null ? (int) $row->user_id : null,
                    'user_name' => $row->user_name,
                    'user_avatar' => $row->user_avatar,
                ];
            });

        $commentsByReview = $commentRows->groupBy('review_id');

        foreach ($reviews as $review) {
            if (!$review) {
                continue;
            }

            $reviewComments = $commentsByReview->get((int) $review->id, collect());
            $review->comments = $this->buildCommentTree($reviewComments);
        }

        return $reviews;
    }

    private function buildCommentTree(Collection $comments): array
    {
        if ($comments->isEmpty()) {
            return [];
        }

        $groupedByParent = $comments->groupBy(function (array $comment) {
            return $comment['parent_id'] !== null ? (string) $comment['parent_id'] : 'root';
        });

        $buildBranch = function (string $parentKey) use (&$buildBranch, $groupedByParent): array {
            return $groupedByParent
                ->get($parentKey, collect())
                ->map(function (array $comment) use (&$buildBranch) {
                    $comment['replies'] = $buildBranch((string) $comment['id']);
                    return $comment;
                })
                ->values()
                ->all();
        };

        return $buildBranch('root');
    }

    public function byCategory($category)
    {
        $products = Product::where('category', $category)->get();
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
            $productsQuery = Product::where('title', 'like', "%{$q}%")
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
            $query = Product::where(function($q) use ($minDiscount) {
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
