<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Get all cart items for the authenticated user
     */
    public function index(): JsonResponse
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,  // CartItem ID - use this for updates/deletes
                    'cart_item_id' => $item->id,  // Same as id for clarity
                    'product_id' => $item->product_id,
                    'title' => $item->product->title,
                    'price' => $item->product->price,
                    'variant_price' => $item->getVariantPrice(),
                    'image' => $item->product->image,
                    'description' => $item->product->description,
                    'quantity' => $item->quantity,
                    'variant_options' => $item->variant_options ?? [],
                ];
            });

        return response()->json($cartItems);
    }

    /**
     * Add a product to the cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
            'variant_options' => 'nullable|array',
        ]);

        $quantity = $request->quantity ?? 1;
        $variantOptions = $request->variant_options ?? [];
        
        \Log::info('Cart Add Request', [
            'product_id' => $request->product_id,
            'quantity' => $quantity,
            'variant_options' => $variantOptions,
            'user_id' => auth()->id(),
        ]);

        // Normalize variant options - ensure consistent format
        $normalizedVariants = !empty($variantOptions) ? $variantOptions : null;

        // Find existing cart item with same product_id AND same variant_options
        $cartItems = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->get();

        $existingCartItem = null;
        foreach ($cartItems as $item) {
            if ($this->variantsMatch($item->variant_options, $normalizedVariants)) {
                $existingCartItem = $item;
                break;
            }
        }

        if ($existingCartItem) {
            // Item with same variants exists - increase quantity
            \Log::info('Cart: Found existing item, incrementing quantity', [
                'existing_id' => $existingCartItem->id,
                'new_quantity' => $existingCartItem->quantity + $quantity,
            ]);
            $existingCartItem->update(['quantity' => $existingCartItem->quantity + $quantity]);
            $cartItem = $existingCartItem;
        } else {
            // Create new cart item (different product or different variants)
            \Log::info('Cart: Creating new item', [
                'product_id' => $request->product_id,
                'variant_options' => $normalizedVariants,
            ]);
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $quantity,
                'variant_options' => $normalizedVariants,
            ]);
        }

        // Reload the cart item with product to calculate prices
        $cartItem->load('product');

        return response()->json([
            'message' => __('messages.cart.item_added'),
            'cart_item' => [
                'id' => $cartItem->product_id,
                'product_id' => $cartItem->product_id,
                'cart_item_id' => $cartItem->id,
                'title' => $cartItem->product->title,
                'price' => $cartItem->product->price,
                'variant_price' => $cartItem->getVariantPrice(),
                'quantity' => $cartItem->quantity,
                'variant_options' => $cartItem->variant_options ?? [],
            ],
        ], 201);
    }

    /**
     * Helper method to compare variant options
     */
    private function variantsMatch($variant1, $variant2): bool
    {
        // Handle null/empty cases
        if (is_null($variant1) && is_null($variant2)) {
            return true;
        }
        
        if ((is_null($variant1) || empty($variant1)) && (is_null($variant2) || empty($variant2))) {
            return true;
        }

        if (is_null($variant1) || is_null($variant2)) {
            return false;
        }

        // Convert to arrays if needed
        $v1 = is_array($variant1) ? $variant1 : (is_string($variant1) ? json_decode($variant1, true) : []);
        $v2 = is_array($variant2) ? $variant2 : (is_string($variant2) ? json_decode($variant2, true) : []);

        // If either is empty or not an array, they don't match (unless both are empty, handled above)
        if (!is_array($v1) || !is_array($v2)) {
            return false;
        }

        // Sort both arrays by key and compare as JSON
        ksort($v1);
        ksort($v2);

        return json_encode($v1) === json_encode($v2);
    }

    /**
     * Update cart item quantity and/or variants
     */
    public function update(Request $request, $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_options' => 'nullable|array',
        ]);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('id', $cartItemId)
            ->orWhere('product_id', $cartItemId)
            ->firstOrFail();

        $updateData = [];
        
        if ($request->has('quantity')) {
            $updateData['quantity'] = $request->quantity;
        }
        
        if ($request->has('variant_options')) {
            $updateData['variant_options'] = count($request->variant_options) > 0 ? $request->variant_options : null;
        }

        if (!empty($updateData)) {
            $cartItem->update($updateData);
        }

        // Reload to include product relationship
        $cartItem->load('product');

        return response()->json([
            'message' => __('messages.cart.item_updated'),
            'cart_item' => [
                'id' => $cartItem->product_id,
                'product_id' => $cartItem->product_id,
                'cart_item_id' => $cartItem->id,
                'title' => $cartItem->product->title,
                'price' => $cartItem->product->price,
                'variant_price' => $cartItem->getVariantPrice(),
                'quantity' => $cartItem->quantity,
                'variant_options' => $cartItem->variant_options ?? [],
            ],
        ]);
    }

    /**
     * Remove a product from the cart
     */
    public function remove($cartItemId): JsonResponse
    {
        CartItem::where('user_id', auth()->id())
            ->where('id', $cartItemId)
            ->firstOrFail()
            ->delete();

        return response()->json(['message' => __('messages.cart.item_removed')]);
    }

    /**
     * Clear the entire cart
     */
    public function clear(): JsonResponse
    {
        CartItem::where('user_id', auth()->id())->delete();

        return response()->json(['message' => __('messages.cart.cleared')]);
    }
}
