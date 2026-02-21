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
                $userId = null;

                // If a Sanctum personal access token was provided in Authorization header, resolve it
                try {
                    if ($request->bearerToken()) {
                        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
                        if ($tokenModel && $tokenModel->tokenable) {
                            $userId = $tokenModel->tokenable->id;
                        }
                    }
                } catch (\Exception $e) {
                    // ignore and fallback to auth()->id()
                }

                if (!$userId) {
                    $userId = auth()->id();
                }

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
                    'message' => __('messages.order.created_successfully'),
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
                'message' => __('messages.order.create_failed', ['error' => $e->getMessage()])
            ], 400);
        }
    }

    /**
     * Get user's orders.
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['message' => __('messages.order.unauthorized')], 401);
        }

        $orders = auth()->user()->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference' => $order->reference,
                    'status' => $order->status,
                    'total' => (float)$order->total,
                    'created_at' => $order->created_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product?->title ?? 'Deleted Product',
                            'quantity' => $item->quantity,
                            'price' => (float)$item->price,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Get a specific order.
     */
    public function show(Order $order)
    {
        if (!auth()->check() || $order->user_id !== auth()->id()) {
            return response()->json(['message' => __('messages.order.unauthorized')], 403);
        }

        $order->load('items.product', 'user');

        $address = is_string($order->address) ? json_decode($order->address, true) : $order->address;

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'status' => $order->status,
                'total' => (float)$order->total,
                'created_at' => $order->created_at,
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'address' => $address,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product?->title ?? 'Deleted Product',
                        'quantity' => $item->quantity,
                        'price' => (float)$item->price,
                    ];
                }),
            ],
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
            'message' => __('messages.order.updated_successfully'),
            'order' => $order
        ]);
    }

    /**
     * Send confirmation email for order
     */
    public function sendConfirmationEmail(Request $request, Order $order)
    {
        // Only allow if user owns the order or is admin
        if ($order->user_id && !auth()->check()) {
            return response()->json(['message' => __('messages.order.unauthorized')], 403);
        }

        if ($order->user_id && auth()->id() !== $order->user_id) {
            return response()->json(['message' => __('messages.order.unauthorized')], 403);
        }

        try {
            // Get email from order delivery address
            $address = $order->address;
            $email = $address['email'] ?? null;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.order.no_email')
                ], 400);
            }

            // Send email
            \Mail::send('emails.order-confirmation', [
                'order' => $order,
                'address' => $address,
                'phone' => $order->phone ?? 'N/A',
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_title' => $item->product->title ?? 'Product',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->toArray(),
                'paymentMethod' => [
                    'card' => 'Platobná karta',
                    'googlepay' => 'Google Pay',
                    'paypal' => 'PayPal',
                ][$order->payment_method] ?? $order->payment_method,
            ], function ($message) use ($email, $order) {
                $message->to($email)
                    ->subject("Potvrdenie objednávky - {$order->reference}");
            });

            return response()->json([
                'success' => true,
                'message' => __('messages.order.confirmation_email_sent')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.order.email_send_failed', ['error' => $e->getMessage()])
            ], 500);
        }
    }}