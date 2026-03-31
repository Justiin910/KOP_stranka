<?php

namespace App\Http\Controllers;

use App\Models\PaymentCard;
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
            'save_card' => 'nullable|boolean',
            'card_details' => 'nullable|array',
            'card_details.cardholder_name' => 'nullable|string|max:120',
            'card_details.card_number' => 'nullable|string',
            'card_details.expiry' => [
                'nullable',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
            ],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.variant_options' => 'nullable|array',
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
                        'variant_options' => $item['variant_options'] ?? null,
                    ]);
                }

                // Clear user's cart if authenticated
                if ($userId) {
                    DB::table('cart_items')->where('user_id', $userId)->delete();

                    $shouldSaveCard = ($validated['save_card'] ?? false) && $validated['payment_method'] === 'card';
                    $cardDetails = $validated['card_details'] ?? null;
                    if ($shouldSaveCard && $cardDetails) {
                        $this->saveDefaultPaymentCard($userId, $cardDetails);
                    }
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
                            'product_image' => $item->product?->image,
                            'quantity' => $item->quantity,
                            'price' => (float)$item->price,
                            'variant_options' => $item->variant_options ?? [],
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
                        'product_image' => $item->product?->image,
                        'quantity' => $item->quantity,
                        'price' => (float)$item->price,
                        'variant_options' => $item->variant_options ?? [],
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
            $order->loadMissing('items.product');

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
            $paymentMethodLabels = trans('messages.email.order_confirmation.payment_methods');
            $paymentMethodLabel = is_array($paymentMethodLabels)
                ? ($paymentMethodLabels[$order->payment_method] ?? $order->payment_method)
                : $order->payment_method;

            \Mail::send('emails.order-confirmation', [
                'order' => $order,
                'address' => $address,
                'phone' => $order->phone ?? 'N/A',
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_title' => $item->product->title ?? 'Product',
                        'product_image' => $item->product?->image,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'variant_options' => $item->variant_options ?? null,
                    ];
                })->toArray(),
                'paymentMethod' => [
                    'label' => $paymentMethodLabel,
                ],
            ], function ($message) use ($email, $order) {
                $message->to($email)
                    ->subject(__('messages.email.order_confirmation.subject', ['reference' => $order->reference]));
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
    }

    private function saveDefaultPaymentCard(int $userId, array $cardDetails): void
    {
        $digits = preg_replace('/\D+/', '', (string) ($cardDetails['card_number'] ?? ''));
        $expiry = (string) ($cardDetails['expiry'] ?? '');
        $name = trim((string) ($cardDetails['cardholder_name'] ?? ''));

        if (strlen($digits) < 13 || strlen($digits) > 19 || !preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry) || $name === '') {
            return;
        }

        [$monthRaw, $yearRaw] = explode('/', $expiry);

        PaymentCard::where('user_id', $userId)->update(['is_default' => false]);

        PaymentCard::create([
            'user_id' => $userId,
            'cardholder_name' => $name,
            'card_number' => $digits,
            'expiry_month' => (int) $monthRaw,
            'expiry_year' => 2000 + (int) $yearRaw,
            'brand' => $this->detectCardBrand($digits),
            'last4' => substr($digits, -4),
            'is_default' => true,
        ]);
    }

    private function detectCardBrand(string $digits): string
    {
        if (preg_match('/^4/', $digits)) {
            return 'visa';
        }
        if (preg_match('/^(5[1-5]|2[2-7])/', $digits)) {
            return 'mastercard';
        }
        if (preg_match('/^3[47]/', $digits)) {
            return 'amex';
        }

        return 'card';
    }
}