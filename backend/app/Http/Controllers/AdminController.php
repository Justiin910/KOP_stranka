<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Get all users for admin panel
     */
    public function getUsers()
    {
        // Get all users with their orders
        $users = User::with('orders')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'user',
                    'phone' => $user->phone,
                    'orders' => $user->orders->count(),
                    'totalSpent' => (int)$user->orders->sum('total'),
                    'registered' => $user->created_at->format('d.m.Y'),
                ];
            });

        return response()->json($users);
    }

    /**
     * Get single user details
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'user',
            'phone' => $user->phone,
            'created_at' => $user->created_at->format('d.m.Y'),
            'updated_at' => $user->updated_at->format('d.m.Y'),
        ]);
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            // Use RFC + DNS checks for admin email updates
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        // Only owners may change roles
        if (Auth::user() && Auth::user()->role === 'owner') {
            $rules['role'] = 'in:user,admin,owner';
        }

        $validated = $request->validate($rules);

        // Defensive MX-only DNS check for updated email address
        $emailToCheck = $validated['email'] ?? null;
        if ($emailToCheck) {
            $domain = substr(strrchr($emailToCheck, '@'), 1);
            if ($domain) {
                $hasMx = function_exists('checkdnsrr') && checkdnsrr($domain, 'MX');
                if (!$hasMx) {
                    return response()->json(['message' => 'E-mailová doména nemá MX záznamy alebo je neplatná.'], 422);
                }
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        // Handle role change if provided and allowed
        if (isset($validated['role']) && Auth::user() && Auth::user()->role === 'owner') {
            // Prevent owner from demoting themselves
            if (Auth::id() === $user->id && $validated['role'] !== 'owner') {
                return response()->json(['message' => 'Nemôžete si odobrať rolu owner.'], 403);
            }
            $user->role = $validated['role'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Používateľ bol aktualizovaný',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role ?? 'user',
            ]
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Používateľ bol vymazaný'
        ]);
    }

    /**
     * Reset user password (sends temporary password via email)
     */
    public function resetUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Generate temporary password
        $tempPassword = \Illuminate\Support\Str::random(12);
        $hashedPassword = Hash::make($tempPassword);
        $user->password = $hashedPassword;
        $user->save();

        // Log for debugging
        \Log::info("Temp password generated for user {$id} ({$user->email}). Password: {$tempPassword}");

        // Send email with temporary password
        try {
            $htmlBody = "
<html>
<head><meta charset='utf-8'></head>
<body style='font-family: Arial; line-height: 1.6; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px;'>
            <h1>Vaše dočasné heslo</h1>
        </div>
        
        <div style='background: #f9f9f9; padding: 20px; border-radius: 5px; margin-top: 20px;'>
            <p>Dobrý deň <strong>{$user->name}</strong>,</p>
            
            <p>Administrátor vám vygeneroval nové dočasné heslo na prístup do vášho konta.</p>
            
            <div style='background: white; border: 2px solid #667eea; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0; color: #666; font-size: 12px;'>Dočasné heslo:</p>
                <code style='font-size: 18px; font-weight: bold; color: #667eea; font-family: Courier New, monospace;'>{$tempPassword}</code>
            </div>
            
            <h3>Ako sa prihlásiť:</h3>
            <ol>
                <li>Prejdite na prihlásovaciu stránku</li>
                <li>Zadajte vašu emailovú adresu</li>
                <li>Zadajte toto dočasné heslo</li>
                <li>Po prihlásení si prosím zmeniť heslo na nové silné heslo</li>
            </ol>
            
            <p style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; border-radius: 3px;'>
                <strong>⚠️ Bezpečnosť:</strong> Toto heslo je dočasné. Zmeniť si ho môžete v nastaveniach svojho profilu.
            </p>
        </div>
        
        <div style='text-align: center; color: #666; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
            <p>Ak ste si nepožiadali toto heslo alebo máte otázky, kontaktujte nás.</p>
            <p>&copy; " . date('Y') . " " . config('app.name') . ". Všetky práva vyhradené.</p>
        </div>
    </div>
</body>
</html>
            ";
            
            \Mail::html($htmlBody, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Vaše dočasné heslo - Obnovenie prístupu')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            \Log::info("Email successfully sent to {$user->email}");
        } catch (\Exception $e) {
            \Log::error('Failed to send temp password email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // Send password change notification email (since password was reset)
        try {
            $changeTime = now()->format('d.m.Y H:i');
            $htmlBody = view('emails.password-changed', [
                'user' => $user,
                'changeTime' => $changeTime
            ])->render();

            \Mail::html($htmlBody, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Vaše heslo bolo zmenené')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Log::info("Password change notification email sent to user {$id} ({$user->email})");
        } catch (\Exception $e) {
            \Log::error("Failed to send password change notification email for user {$id}: " . $e->getMessage());
            // Don't fail the password reset if email fails, just log it
        }
        
        return response()->json([
            'message' => 'Dočasné heslo bolo odoslané na ' . $user->email
        ]);
    }

    /**
     * Set user password to a specific value (from admin-generated password)
     */
    public function setUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        \Log::info('Setting password for user ' . $id . ' (email: ' . $user->email . ')');
        \Log::info('Password length: ' . strlen($validated['password']));
        
        $hashedPassword = Hash::make($validated['password']);
        $user->password = $hashedPassword;
        $user->save();

        \Log::info('Password saved successfully');

        return response()->json([
            'message' => 'Heslo používateľa bolo nastavené'
        ]);
    }

    /**
     * Get admin statistics
     */
    public function getStats()
    {
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'totalRevenue' => round($totalRevenue, 2),
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'avgOrderValue' => round($avgOrderValue, 2),
        ]);
    }

    /**
     * Get all products for admin panel
     */
    public function getProducts()
    {
        $products = \App\Models\Product::all();
        return response()->json($products);
    }

    /**
     * Create a new product
     */
    public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'oldPrice' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        // Smart price handling: if discount is applied without oldPrice, use price as oldPrice
        $finalPrice = $validated['price'];
        $finalOldPrice = $validated['oldPrice'] ?? null;
        $finalDiscountType = $validated['discount_type'] ?? null;
        $finalDiscountValue = $validated['discount_value'] ?? null;

        if ($finalDiscountValue && $finalDiscountValue > 0 && (!$finalOldPrice || $finalOldPrice == 0)) {
            // Use current price as old price
            $finalOldPrice = $finalPrice;
            
            // Calculate new price from discount
            if ($finalDiscountType === 'percent') {
                $finalPrice = $finalOldPrice - ($finalOldPrice * $finalDiscountValue / 100);
            } else { // fixed
                $finalPrice = $finalOldPrice - $finalDiscountValue;
            }
            $finalPrice = round($finalPrice, 2);
        } elseif (!$finalDiscountValue || $finalDiscountValue == 0) {
            // If no discount, clear oldPrice
            $finalOldPrice = null;
            $finalDiscountType = null;
            $finalDiscountValue = null;
        }

        // Map 'name' to 'title' for database
        $data = [
            'title' => $validated['name'],
            'brand' => $validated['brand'],
            'category' => $validated['category'],
            'price' => $finalPrice,
            'oldPrice' => $finalOldPrice,
            'stock' => $validated['stock'],
            'image' => $validated['image'] ?? null,
            'description' => $validated['description'] ?? null,
            'discount_type' => $finalDiscountType,
            'discount_value' => $finalDiscountValue,
        ];

        $product = \App\Models\Product::create($data);

        return response()->json([
            'message' => 'Produkt bol vytvorený',
            'product' => $product
        ], 201);
    }

    /**
     * Update a product
     */
    public function updateProduct(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'oldPrice' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        // Smart price handling: when discount is applied, calculate final price from oldPrice
        $finalPrice = $validated['price'];
        $finalOldPrice = $validated['oldPrice'] ?? null;
        $finalDiscountType = $validated['discount_type'] ?? null;
        $finalDiscountValue = $validated['discount_value'] ?? null;

        if ($finalDiscountValue && $finalDiscountValue > 0) {
            // When discount is applied, oldPrice must be set (use current price if not provided)
            if (!$finalOldPrice || $finalOldPrice == 0) {
                $finalOldPrice = $finalPrice;
            }
            
            // Always calculate new price from discount when discount is applied
            if ($finalDiscountType === 'percent') {
                $finalPrice = $finalOldPrice - ($finalOldPrice * $finalDiscountValue / 100);
            } else { // fixed
                $finalPrice = $finalOldPrice - $finalDiscountValue;
            }
            $finalPrice = round($finalPrice, 2);
        } elseif (!$finalDiscountValue || $finalDiscountValue == 0) {
            // If no discount, clear oldPrice
            $finalOldPrice = null;
            $finalDiscountType = null;
            $finalDiscountValue = null;
        }

        $data = [
            'title' => $validated['name'],
            'brand' => $validated['brand'],
            'category' => $validated['category'],
            'price' => $finalPrice,
            'oldPrice' => $finalOldPrice,
            'stock' => $validated['stock'],
            'image' => $validated['image'] ?? $product->image,
            'description' => $validated['description'] ?? $product->description,
            'discount_type' => $finalDiscountType,
            'discount_value' => $finalDiscountValue,
        ];

        $product->update($data);

        return response()->json([
            'message' => 'Produkt bol aktualizovaný',
            'product' => $product
        ]);
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produkt bol vymazaný'
        ]);
    }

    /**
     * Get all orders for admin panel
     */
    public function getOrders()
    {
        $orders = Order::with('items.product', 'user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                // Get customer name - from user if exists, otherwise from address
                $address = is_string($order->address) ? json_decode($order->address, true) : $order->address;
                $customerName = $order->user ? $order->user->name : ($address['fullName'] ?? 'Guest');
                
                // Normalize and map status values to Slovak display labels
                $rawStatus = strtolower(trim((string)($order->status ?? '')));
                $displayStatus = '';
                if (in_array($rawStatus, ['pending', 'čakajúce', 'cakajuce', 'cakajuce'])) {
                    $displayStatus = 'čakajúce';
                } elseif (in_array($rawStatus, ['processing', 'spracováva sa', 'spracovava sa', 'spracovava_sa'])) {
                    $displayStatus = 'Spracováva sa';
                } elseif (in_array($rawStatus, ['shipped', 'in transit', 'v preprave', 'v_preprave'])) {
                    $displayStatus = 'V preprave';
                } elseif (in_array($rawStatus, ['delivered', 'doručené', 'dorucene'])) {
                    $displayStatus = 'Doručené';
                } elseif (in_array($rawStatus, ['cancelled', 'canceled', 'zrušené', 'zrusene'])) {
                    $displayStatus = 'Zrušené';
                } else {
                    // Fallback: use original status with ucfirst
                    $displayStatus = $order->status ? ucfirst($order->status) : '';
                }

                return [
                    'id' => $order->id,
                    'reference' => $order->reference,
                    'customerName' => $customerName,
                    'email' => $order->user ? $order->user->email : ($address['email'] ?? 'N/A'),
                    'items' => $order->items->count(),
                    'total' => (float)$order->total,
                    'status' => $displayStatus,
                    'delivery_method' => $order->delivery_method,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->format('d.m.Y H:i'),
                    'address' => $address,
                    'order_items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->title ?? 'Deleted Product',
                            'quantity' => $item->quantity,
                            'price' => (float)$item->price,
                        ];
                    }),
                ];
            });

        return response()->json($orders);

    }

    /**
     * Delete an order
     */
    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => 'Objednávka bola vymazaná'
        ]);
    }

    /**
     * Update an order
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'status' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'delivery_method' => 'nullable|string',
            'address' => 'nullable|array',
            'address.fullName' => 'nullable|string',
            'address.email' => 'nullable|email',
            'address.phone' => 'nullable|string',
            'address.street' => 'nullable|string',
            'address.city' => 'nullable|string',
            'address.zip' => 'nullable|string',
            'address.country' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.product_id' => 'nullable|integer',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        // Update basic order info
        if (isset($validated['status'])) {
            $order->status = $validated['status'];
        }
        if (isset($validated['payment_method'])) {
            $order->payment_method = $validated['payment_method'];
        }
        if (isset($validated['delivery_method'])) {
            $order->delivery_method = $validated['delivery_method'];
        }
        if (isset($validated['address'])) {
            $order->address = json_encode($validated['address']);
        }

        // Update items if provided
        if (isset($validated['items'])) {
            $itemIds = [];
            $newTotal = 0;

            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = $order->items()->find($itemData['id']);
                    if ($item) {
                        $item->quantity = $itemData['quantity'] ?? $item->quantity;
                        $item->price = $itemData['price'] ?? $item->price;
                        $item->save();
                        $itemIds[] = $item->id;
                        $newTotal += $item->quantity * $item->price;
                    }
                }
            }

            // Delete items not in the update
            $order->items()->whereNotIn('id', $itemIds)->delete();

            // Update order total
            $order->total = $newTotal;
        }

        $order->save();

        // Return updated order
        $order = $order->refresh();
        $address = is_string($order->address) ? json_decode($order->address, true) : $order->address;
        $customerName = $order->user ? $order->user->name : ($address['fullName'] ?? 'Guest');

        return response()->json([
            'message' => 'Objednávka bola aktualizovaná',
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'customerName' => $customerName,
                'email' => $order->user ? $order->user->email : ($address['email'] ?? 'N/A'),
                'items' => $order->items->count(),
                'total' => (float)$order->total,
                'status' => ucfirst($order->status),
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->format('d.m.Y H:i'),
                'address' => $address,
                'order_items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Deleted Product',
                        'quantity' => $item->quantity,
                        'price' => (float)$item->price,
                    ];
                }),
            ]
        ]);
    }
}
