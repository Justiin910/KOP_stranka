<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create a new order from checkout data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|unique:orders,reference',
            'delivery_method' => 'required|in:standard,express,pickup',
            'address' => 'required|json',
            'phone' => 'required|string',
            'payment_method' => 'required|in:card,googlepay,paypal',
            'payment_token' => 'required|string',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Get authenticated user (if any)
                $userId = auth()->id();

                // Parse address if it's a string
                $address = is_string($validated['address'])
                    ? json_decode($validated['address'], true)
                    : $validated['address'];

                // Create order
                $order = Order::create([
                    'user_id' => $userId,
                    'reference' => $validated['reference'],
                    'delivery_method' => $validated['delivery_method'],
                    'address' => $address,
                    'phone' => $validated['phone'],
                    'payment_method' => $validated['payment_method'],
                    'payment_token' => $validated['payment_token'],
                    'total' => $validated['total'],
                    'status' => 'pending', // Will be updated to 'paid' after payment verification
                ]);

                // Create order items
                foreach ($validated['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                // Clear user's cart if authenticated
                if ($userId) {
                    DB::table('cart_items')->where('user_id', $userId)->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order' => [
                        'id' => $order->id,
                        'reference' => $order->reference,
                        'total' => $order->total,
                        'status' => $order->status,
                        'created_at' => $order->created_at,
                    ]
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's orders.
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = auth()->user()->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Get a specific order.
     */
    public function show(Order $order)
    {
        if (!auth()->check() || $order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Update order status (admin only).
     */
    public function updateStatus(Request $request, Order $order)
    {
        // In production, add admin authorization check
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled,shipped,delivered'
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'order' => $order
        ]);
    }
}
