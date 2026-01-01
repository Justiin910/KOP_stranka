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
                    'id' => $item->product_id,
                    'product_id' => $item->product_id,
                    'cart_item_id' => $item->id,
                    'title' => $item->product->title,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'description' => $item->product->description,
                    'quantity' => $item->quantity,
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
        ]);

        $quantity = $request->quantity ?? 1;
        $existingQuantity = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->value('quantity') ?? 0;

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $existingQuantity + $quantity,
            ]
        );

        return response()->json([
            'message' => 'Item added to cart',
            'cart_item' => $cartItem,
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->firstOrFail();

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Cart item updated',
            'cart_item' => $cartItem,
        ]);
    }

    /**
     * Remove a product from the cart
     */
    public function remove($productId): JsonResponse
    {
        CartItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Clear the entire cart
     */
    public function clear(): JsonResponse
    {
        CartItem::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
