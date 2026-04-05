<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

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
                    'avatar' => $user->avatar ?? null,
                    'two_factor_enabled' => (bool) $user->two_factor_enabled,
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
            'avatar' => $user->avatar ?? null,
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
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
        $beforeSnapshot = $this->snapshotUserProfileForEmail($user);

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            // Use RFC + DNS checks for admin email updates
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'remove_avatar' => 'nullable|boolean',
            'two_factor_enabled' => 'nullable|boolean',
        ];

        // Only owners may change roles
        if (Auth::user() && Auth::user()->role === 'owner') {
            $rules['role'] = 'in:user,admin,owner';
        }

        $validated = $request->validate($rules);

        // Additional MX check for updated email domain
        $emailToCheck = $validated['email'] ?? null;
        if ($emailToCheck) {
            $domain = substr(strrchr($emailToCheck, '@'), 1);
            if ($domain) {
                $hasMx = function_exists('checkdnsrr') && checkdnsrr($domain, 'MX');
                if (!$hasMx) {
                    throw ValidationException::withMessages([
                        'email' => [__('admin.user.invalid_domain')],
                    ]);
                }
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (($validated['remove_avatar'] ?? false) && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        }

        if (array_key_exists('two_factor_enabled', $validated)) {
            $user->two_factor_enabled = (bool) $validated['two_factor_enabled'];

            // When 2FA is disabled by admin, clear any active/pending codes.
            if (!$user->two_factor_enabled) {
                $user->two_factor_code = null;
                $user->two_factor_expires_at = null;
                $user->two_factor_purpose = null;
                $user->two_factor_pending_email = null;
            }
        }

        // Handle role change if provided and allowed
        if (isset($validated['role']) && Auth::user() && Auth::user()->role === 'owner') {
            // Prevent owner from demoting themselves
            if (Auth::id() === $user->id && $validated['role'] !== 'owner') {
                return response()->json(['message' => __('admin.user.cannot_remove_owner')], 403);
            }
            $user->role = $validated['role'];
        }

        $user->save();

        $afterSnapshot = $this->snapshotUserProfileForEmail($user);
        $changeLines = $this->buildUserProfileChangeLines($beforeSnapshot, $afterSnapshot, [
            'avatar_removed' => ($validated['remove_avatar'] ?? false) && !empty($beforeSnapshot['avatar_path']),
            'password_changed' => false,
        ]);

        if (!empty($changeLines)) {
            $this->sendUserUpdatedEmail($user, $changeLines);
        }

        if (($beforeSnapshot['two_factor_enabled'] ?? false) !== ($afterSnapshot['two_factor_enabled'] ?? false)) {
            $this->sendTwoFactorStatusEmail($user, (bool) $afterSnapshot['two_factor_enabled']);
        }

        return response()->json([
            'message' => __('admin.user.updated'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role ?? 'user',
                'avatar' => $user->avatar ?? null,
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
            ]
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        $email = trim((string) ($user->email ?? ''));
        $userName = (string) ($user->name ?? '');
        $locale = $this->resolveEmailLocale($user->language ?? null);

        $user->delete();

        if ($email !== '') {
            $this->sendAccountDeletedEmail($email, $userName, $locale);
        }

        return response()->json([
            'message' => __('admin.user.deleted')
        ]);
    }

    private function snapshotUserProfileForEmail(User $user): array
    {
        return [
            'name' => trim((string) ($user->name ?? '')),
            'email' => trim((string) ($user->email ?? '')),
            'phone' => trim((string) ($user->phone ?? '')),
            'role' => trim((string) ($user->role ?? 'user')),
            'two_factor_enabled' => (bool) ($user->two_factor_enabled ?? false),
            'avatar_path' => trim((string) ($user->avatar ?? '')),
        ];
    }

    private function buildUserProfileChangeLines(array $before, array $after, array $context = []): array
    {
        $lines = [];

        $fieldMap = [
            'name' => 'messages.email.user_updated.field_name',
            'email' => 'messages.email.user_updated.field_email',
            'phone' => 'messages.email.user_updated.field_phone',
            'role' => 'messages.email.user_updated.field_role',
        ];

        foreach ($fieldMap as $field => $labelKey) {
            $oldValue = trim((string) ($before[$field] ?? ''));
            $newValue = trim((string) ($after[$field] ?? ''));

            if ($this->normalizeComparableText($oldValue) !== $this->normalizeComparableText($newValue)) {
                $lines[] = __('messages.email.user_updated.change_field', [
                    'field' => __($labelKey),
                    'old' => $oldValue !== '' ? $oldValue : '—',
                    'new' => $newValue !== '' ? $newValue : '—',
                ]);
            }
        }

        if (($context['avatar_removed'] ?? false) === true) {
            $lines[] = __('messages.email.user_updated.change_avatar_removed');
        }

        if (($context['password_changed'] ?? false) === true) {
            $lines[] = __('messages.email.user_updated.change_password_set_by_admin');
        }

        return array_values(array_unique($lines));
    }

    private function resolveEmailLocale(?string $preferredLocale): string
    {
        $fallback = app()->getLocale();
        $preferred = is_string($preferredLocale) ? strtolower(trim($preferredLocale)) : '';

        if (in_array($preferred, ['sk', 'en'], true)) {
            return $preferred;
        }

        return in_array($fallback, ['sk', 'en'], true) ? $fallback : 'sk';
    }

    private function runWithEmailLocale(string $locale, callable $callback): void
    {
        $currentLocale = app()->getLocale();
        app()->setLocale($locale);

        try {
            $callback();
        } finally {
            app()->setLocale($currentLocale);
        }
    }

    private function sendUserUpdatedEmail(User $user, array $changeLines): void
    {
        $email = trim((string) ($user->email ?? ''));
        if ($email === '') {
            return;
        }

        $locale = $this->resolveEmailLocale($user->language ?? null);

        try {
            $this->runWithEmailLocale($locale, function () use ($email, $user, $changeLines) {
                Mail::send('emails.user-updated', [
                    'userName' => (string) ($user->name ?? ''),
                    'changeLines' => $changeLines,
                    'changedAt' => now()->format('d.m.Y H:i'),
                ], function ($message) use ($email) {
                    $message->to($email)
                        ->subject(__('messages.email.user_updated.subject'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send user-updated email', [
                'user_id' => $user->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendAccountDeletedEmail(string $email, string $userName, string $locale): void
    {
        try {
            $this->runWithEmailLocale($locale, function () use ($email, $userName) {
                Mail::send('emails.account-deleted', [
                    'userName' => $userName,
                    'deletedAt' => now()->format('d.m.Y H:i'),
                ], function ($message) use ($email) {
                    $message->to($email)
                        ->subject(__('messages.email.account_deleted.subject'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send account-deleted email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTwoFactorStatusEmail(User $user, bool $enabled): void
    {
        $email = trim((string) ($user->email ?? ''));
        if ($email === '') {
            return;
        }

        $throttleKey = 'email:two-factor-status:' . (int) $user->id;
        $maxAttempts = 1;
        $cooldownSeconds = 300;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            Log::info('Two-factor status email skipped due to cooldown', [
                'user_id' => $user->id,
                'enabled' => $enabled,
                'available_in' => RateLimiter::availableIn($throttleKey),
            ]);
            return;
        }

        RateLimiter::hit($throttleKey, $cooldownSeconds);

        $locale = $this->resolveEmailLocale($user->language ?? null);

        try {
            $this->runWithEmailLocale($locale, function () use ($email, $user, $enabled) {
                Mail::send('emails.two-factor-status', [
                    'userName' => (string) ($user->name ?? ''),
                    'enabled' => $enabled,
                    'changedAt' => now()->format('d.m.Y H:i'),
                ], function ($message) use ($email, $enabled) {
                    $subjectKey = $enabled
                        ? 'messages.email.two_factor_status.subject_enabled'
                        : 'messages.email.two_factor_status.subject_disabled';

                    $message->to($email)
                        ->subject(__($subjectKey))
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send two-factor status email', [
                'user_id' => $user->id,
                'email' => $email,
                'enabled' => $enabled,
                'error' => $e->getMessage(),
            ]);
        }
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
            $htmlBody = view('emails.temp-password', [
                'userName' => $user->name,
                'tempPassword' => $tempPassword,
            ])->render();
            
            \Mail::html($htmlBody, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject(__('messages.email.temp_password.subject'))
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
                        ->subject(__('messages.email.password_changed.subject'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Log::info("Password change notification email sent to user {$id} ({$user->email})");
        } catch (\Exception $e) {
            \Log::error("Failed to send password change notification email for user {$id}: " . $e->getMessage());
            // Don't fail the password reset if email fails, just log it
        }
        
        return response()->json([
            'message' => __('admin.user.temp_password_sent', ['email' => $user->email])
        ]);
    }

    /**
     * Set user password to a specific value (from admin-generated password)
     */
    public function setUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'password' => ['required', Password::defaults()],
        ]);

        \Log::info('Setting password for user ' . $id . ' (email: ' . $user->email . ')');
        \Log::info('Password length: ' . strlen($validated['password']));
        
        $hashedPassword = Hash::make($validated['password']);
        $user->password = $hashedPassword;
        $user->save();

        \Log::info('Password saved successfully');

        return response()->json([
            'message' => __('admin.user.password_set')
        ]);
    }

    /**
     * Get admin statistics and recent activity
     */
    public function getStats()
    {
        $now = \Carbon\Carbon::now();
        
        // Current month range
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        
        // Last month range
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        
        // Current month statistics
        $currentMonthRevenue = Order::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->sum('total');
        $currentMonthOrders = Order::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $currentMonthUsers = User::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $currentMonthAvgOrderValue = $currentMonthOrders > 0 ? $currentMonthRevenue / $currentMonthOrders : 0;
        
        // Last month statistics
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total');
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $lastMonthUsers = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $lastMonthAvgOrderValue = $lastMonthOrders > 0 ? $lastMonthRevenue / $lastMonthOrders : 0;
        
        // Calculate trend percentages
        $revenueTrend = $lastMonthRevenue > 0 ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100) : 0;
        $ordersTrend = $lastMonthOrders > 0 ? round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100) : 0;
        $usersTrend = $lastMonthUsers > 0 ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100) : 0;
        $avgOrderTrend = $lastMonthAvgOrderValue > 0 ? round((($currentMonthAvgOrderValue - $lastMonthAvgOrderValue) / $lastMonthAvgOrderValue) * 100) : 0;
        
        // Get all-time totals
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get recent orders with user details
        $recentOrders = Order::with('user')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => 'order-' . $order->id,
                    'type' => 'order',
                    'title' => 'Nová objednávka od ' . ($order->user?->name ?? 'Hosť'),
                    'value' => number_format($order->total, 2, ',', ' ') . ' €',
                    'created_at' => $order->created_at->toIso8601String(),
                ];
            });

        // Get recent user registrations
        $recentUsers = User::latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => 'user-' . $user->id,
                    'type' => 'user',
                    'title' => 'Nová registrácia: ' . $user->name,
                    'value' => '',
                    'created_at' => $user->created_at->toIso8601String(),
                ];
            });

        // Merge and sort recent activity by time (most recent first)
        $recentActivity = collect([...$recentOrders, ...$recentUsers])
            ->sortByDesc('created_at')
            ->values()
            ->take(5);

        return response()->json([
            'totalRevenue' => round($totalRevenue, 2),
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'avgOrderValue' => round($avgOrderValue, 2),
            'revenueTrend' => $revenueTrend,
            'ordersTrend' => $ordersTrend,
            'usersTrend' => $usersTrend,
            'avgOrderTrend' => $avgOrderTrend,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Format time difference in Slovak language
     */
    private function formatTimeDifference($dateTime)
    {
        $now = \Carbon\Carbon::now();
        $diff = $now->diffInSeconds($dateTime);

        if ($diff < 60) {
            return 'pred chvíľou';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "pred $minutes " . ($minutes === 1 ? 'min' : 'min');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "pred $hours " . ($hours === 1 ? 'hod' : 'hod');
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "pred $days " . ($days === 1 ? 'deňom' : 'dňami');
        } else {
            return $dateTime->format('d.m.Y');
        }
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
            'specs' => 'nullable|string',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'variants' => 'nullable|json',
            'variant_pricing' => 'nullable|json',
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
            'image' => $validated['image'] ?? '',
            'description' => $validated['description'] ?? null,
            'specs' => $validated['specs'] ?? '',
            'discount_type' => $finalDiscountType,
            'discount_value' => $finalDiscountValue,
            'variants' => $validated['variants'] ?? null,
            'variant_pricing' => $validated['variant_pricing'] ?? null,
        ];

        $product = \App\Models\Product::create($data);

        return response()->json([
            'message' => __('admin.product.created'),
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
            'specs' => 'nullable|string',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'variants' => 'nullable|json',
            'variant_pricing' => 'nullable|json',
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
            'image' => $validated['image'] ?? ($product->image ?? ''),
            'description' => $validated['description'] ?? $product->description,
            'specs' => $validated['specs'] ?? ($product->specs ?? ''),
            'discount_type' => $finalDiscountType,
            'discount_value' => $finalDiscountValue,
            'variants' => $validated['variants'] ?? $product->variants,
            'variant_pricing' => $validated['variant_pricing'] ?? $product->variant_pricing,
        ];

        $product->update($data);

        return response()->json([
            'message' => __('admin.product.updated'),
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
            'message' => __('admin.product.deleted')
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
                $address = $this->normalizeOrderAddress($order->address);
                $linkedUser = $order->user ? [
                    'id' => (int) $order->user->id,
                    'name' => (string) $order->user->name,
                    'email' => (string) $order->user->email,
                ] : null;
                $customerName = $linkedUser && $linkedUser['name'] !== ''
                    ? $linkedUser['name']
                    : ($address['fullName'] !== '' ? $address['fullName'] : 'Guest');
                $customerEmail = $address['email'] !== '' ? $address['email'] : 'N/A';
                
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
                    'email' => $customerEmail,
                    'user_id' => $order->user_id ? (int) $order->user_id : null,
                    'linkedUser' => $linkedUser,
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
                            'variant_options' => $item->variant_options ?? [],
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
        $order = Order::with('items.product', 'user')->findOrFail($id);

        $this->sendOrderDeletedEmail($order);

        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => __('admin.order.deleted')
        ]);
    }

    /**
     * Update an order
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        $beforeSnapshot = [
            'status' => (string) ($order->status ?? ''),
            'payment_method' => (string) ($order->payment_method ?? ''),
            'delivery_method' => (string) ($order->delivery_method ?? ''),
            'address' => $this->normalizeOrderAddress($order->address),
            'total' => (float) ($order->total ?? 0),
            'items' => $this->normalizeOrderItems($order),
        ];

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
            'user_id' => 'nullable|integer|exists:users,id',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.product_id' => 'nullable|integer',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.variant_options' => 'nullable|array',
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
        if ($request->exists('user_id')) {
            $order->user_id = $validated['user_id'] ?? null;
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
                        if (isset($itemData['variant_options'])) {
                            $item->variant_options = count($itemData['variant_options']) > 0 ? $itemData['variant_options'] : null;
                        }
                        $item->save();
                        $itemIds[] = $item->id;
                        $newTotal += $item->quantity * $item->price;
                    }
                } else {
                    // Create new item
                    if (isset($itemData['product_id']) && isset($itemData['quantity']) && isset($itemData['price'])) {
                        $newItem = $order->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                            'variant_options' => $itemData['variant_options'] ?? null,
                        ]);
                        $itemIds[] = $newItem->id;
                        $newTotal += $newItem->quantity * $newItem->price;
                    }
                }
            }

            // Delete items not in the update
            $order->items()->whereNotIn('id', $itemIds)->delete();

            // Update order total
            $order->total = $newTotal;
        }

        $order->save();

        // Refetch the entire order with fresh relationships from database
        $order = Order::with('items.product', 'user')->find($order->id);

        $afterSnapshot = [
            'status' => (string) ($order->status ?? ''),
            'payment_method' => (string) ($order->payment_method ?? ''),
            'delivery_method' => (string) ($order->delivery_method ?? ''),
            'address' => $this->normalizeOrderAddress($order->address),
            'total' => (float) ($order->total ?? 0),
            'items' => $this->normalizeOrderItems($order),
        ];

        $changeLines = $this->buildOrderChangeLines($beforeSnapshot, $afterSnapshot);
        if (!empty($changeLines)) {
            $this->sendOrderUpdateEmail($order, $changeLines);
        }
        
        // Return updated order
        $address = $this->normalizeOrderAddress($order->address);
        $linkedUser = $order->user ? [
            'id' => (int) $order->user->id,
            'name' => (string) $order->user->name,
            'email' => (string) $order->user->email,
        ] : null;
        $customerName = $linkedUser && $linkedUser['name'] !== ''
            ? $linkedUser['name']
            : ($address['fullName'] !== '' ? $address['fullName'] : 'Guest');
        $customerEmail = $address['email'] !== '' ? $address['email'] : 'N/A';

        return response()->json([
            'message' => __('admin.order.updated'),
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'customerName' => $customerName,
                'email' => $customerEmail,
                'user_id' => $order->user_id ? (int) $order->user_id : null,
                'linkedUser' => $linkedUser,
                'items' => $order->items->count(),
                'total' => (float)$order->total,
                'status' => ucfirst($order->status),
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->format('d.m.Y H:i'),
                'address' => $address,
                'order_items' => $order->items->map(function ($item) {
                    // Fetch product fresh from database for each item
                    $product = Product::find($item->product_id);
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $product ? $product->title : 'Deleted Product',
                        'quantity' => $item->quantity,
                        'price' => (float)$item->price,
                        'variant_options' => $item->variant_options ?? [],
                    ];
                }),
            ]
        ]);
    }

    private function normalizeOrderAddress($address): array
    {
        $decoded = is_string($address) ? json_decode($address, true) : $address;
        $decoded = is_array($decoded) ? $decoded : [];

        $fields = ['fullName', 'email', 'phone', 'street', 'city', 'zip', 'country'];
        $normalized = [];

        foreach ($fields as $field) {
            $normalized[$field] = trim((string) ($decoded[$field] ?? ''));
        }

        return $normalized;
    }

    private function normalizeOrderItems(Order $order): array
    {
        return $order->items
            ->map(function ($item) {
                $variantOptions = $this->prepareVariantOptions($item->variant_options);

                return [
                    'id' => (int) $item->id,
                    'product_id' => (int) $item->product_id,
                    'product_name' => (string) ($item->product?->title ?? 'Deleted Product'),
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'variant_options' => $variantOptions,
                ];
            })
            ->sortBy('id')
            ->values()
            ->toArray();
    }

    private function buildOrderChangeLines(array $before, array $after): array
    {
        $lines = [];

        $beforeStatus = $this->normalizeOrderStatusValue((string) ($before['status'] ?? ''));
        $afterStatus = $this->normalizeOrderStatusValue((string) ($after['status'] ?? ''));
        if ($beforeStatus !== $afterStatus) {
            $lines[] = __('messages.email.order_updated.change_status', [
                'old' => $this->formatOrderStatusLabel($before['status']),
                'new' => $this->formatOrderStatusLabel($after['status']),
            ]);
        }

        $beforePaymentMethod = $this->normalizePaymentMethodValue((string) ($before['payment_method'] ?? ''));
        $afterPaymentMethod = $this->normalizePaymentMethodValue((string) ($after['payment_method'] ?? ''));
        if ($beforePaymentMethod !== $afterPaymentMethod) {
            $lines[] = __('messages.email.order_updated.change_payment_method', [
                'old' => $this->formatPaymentMethodLabel($before['payment_method']),
                'new' => $this->formatPaymentMethodLabel($after['payment_method']),
            ]);
        }

        $beforeDeliveryMethod = $this->normalizeDeliveryMethodValue((string) ($before['delivery_method'] ?? ''));
        $afterDeliveryMethod = $this->normalizeDeliveryMethodValue((string) ($after['delivery_method'] ?? ''));
        if ($beforeDeliveryMethod !== $afterDeliveryMethod) {
            $lines[] = __('messages.email.order_updated.change_delivery_method', [
                'old' => $this->formatDeliveryMethodLabel($before['delivery_method']),
                'new' => $this->formatDeliveryMethodLabel($after['delivery_method']),
            ]);
        }

        $beforeTotal = $this->normalizeMoneyValue((float) ($before['total'] ?? 0));
        $afterTotal = $this->normalizeMoneyValue((float) ($after['total'] ?? 0));
        if ($beforeTotal !== $afterTotal) {
            $lines[] = __('messages.email.order_updated.change_total', [
                'old' => number_format($beforeTotal, 2),
                'new' => number_format($afterTotal, 2),
            ]);
        }

        foreach (['fullName', 'email', 'phone', 'street', 'city', 'zip', 'country'] as $field) {
            $oldValue = (string) ($before['address'][$field] ?? '');
            $newValue = (string) ($after['address'][$field] ?? '');
            $oldComparable = $this->normalizeAddressFieldValue($field, $oldValue);
            $newComparable = $this->normalizeAddressFieldValue($field, $newValue);

            if ($oldComparable !== $newComparable) {
                $lines[] = __('messages.email.order_updated.change_address_field', [
                    'field' => __('messages.email.order_updated.address_fields.' . $field),
                    'old' => $oldValue !== '' ? $oldValue : '—',
                    'new' => $newValue !== '' ? $newValue : '—',
                ]);
            }
        }

        $beforeItemsById = collect($before['items'])->keyBy('id');
        $afterItemsById = collect($after['items'])->keyBy('id');

        foreach ($afterItemsById as $id => $newItem) {
            if (!$beforeItemsById->has($id)) {
                $lines[] = __('messages.email.order_updated.change_item_added', [
                    'name' => $newItem['product_name'],
                    'quantity' => $newItem['quantity'],
                    'price' => number_format((float) $newItem['price'], 2),
                ]);
                continue;
            }

            $oldItem = $beforeItemsById->get($id);
            $oldQuantity = (int) ($oldItem['quantity'] ?? 0);
            $newQuantity = (int) ($newItem['quantity'] ?? 0);
            $oldPrice = $this->normalizeMoneyValue((float) ($oldItem['price'] ?? 0));
            $newPrice = $this->normalizeMoneyValue((float) ($newItem['price'] ?? 0));
            $oldVariants = $this->prepareVariantOptions($oldItem['variant_options'] ?? []);
            $newVariants = $this->prepareVariantOptions($newItem['variant_options'] ?? []);

            $qtyChanged = $oldQuantity !== $newQuantity;
            $priceChanged = $oldPrice !== $newPrice;
            $variantChanged = $this->canonicalizeVariantOptions($oldVariants) !== $this->canonicalizeVariantOptions($newVariants);

            if ($qtyChanged || $priceChanged) {
                $lines[] = __('messages.email.order_updated.change_item_updated', [
                    'name' => $newItem['product_name'],
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'old_price' => number_format($oldPrice, 2),
                    'new_price' => number_format($newPrice, 2),
                ]);
            }

            if ($variantChanged) {
                $lines[] = __('messages.email.order_updated.change_item_variants', [
                    'name' => $newItem['product_name'],
                    'old_variants' => $this->formatVariantOptionsForEmail($oldVariants),
                    'new_variants' => $this->formatVariantOptionsForEmail($newVariants),
                ]);
            }
        }

        foreach ($beforeItemsById as $id => $oldItem) {
            if (!$afterItemsById->has($id)) {
                $lines[] = __('messages.email.order_updated.change_item_removed', [
                    'name' => $oldItem['product_name'],
                    'quantity' => $oldItem['quantity'],
                ]);
            }
        }

        return array_values(array_unique($lines));
    }

    private function normalizeComparableText(string $value): string
    {
        $trimmed = trim($value);
        $lower = function_exists('mb_strtolower')
            ? mb_strtolower($trimmed, 'UTF-8')
            : strtolower($trimmed);

        return preg_replace('/\s+/u', ' ', $lower) ?: $lower;
    }

    private function normalizeMoneyValue(float $value): float
    {
        return round($value, 2);
    }

    private function normalizeOrderStatusValue(string $status): string
    {
        $normalized = $this->normalizeComparableText($status);

        $map = [
            'pending' => 'pending',
            'čakajúce' => 'pending',
            'cakajuce' => 'pending',
            'čaká na spracovanie' => 'pending',
            'caka na spracovanie' => 'pending',
            'paid' => 'paid',
            'spracováva sa' => 'paid',
            'spracovava sa' => 'paid',
            'processing' => 'paid',
            'uhradena' => 'paid',
            'uhradená' => 'paid',
            'shipped' => 'shipped',
            'v preprave' => 'shipped',
            'odoslané' => 'shipped',
            'odoslane' => 'shipped',
            'delivered' => 'delivered',
            'doručené' => 'delivered',
            'dorucene' => 'delivered',
            'cancelled' => 'cancelled',
            'canceled' => 'cancelled',
            'zrušené' => 'cancelled',
            'zrusene' => 'cancelled',
        ];

        return $map[$normalized] ?? $normalized;
    }

    private function normalizePaymentMethodValue(string $method): string
    {
        $normalized = $this->normalizeComparableText($method);

        $map = [
            'card' => 'card',
            'payment card' => 'card',
            'card payment' => 'card',
            'platobna karta' => 'card',
            'platobná karta' => 'card',
            'platba kartou' => 'card',
            'googlepay' => 'googlepay',
            'google pay' => 'googlepay',
            'paypal' => 'paypal',
            'bank transfer' => 'bank_transfer',
            'bankovy prevod' => 'bank_transfer',
            'bankový prevod' => 'bank_transfer',
            'cash on delivery' => 'cod',
            'dobierka' => 'cod',
        ];

        return $map[$normalized] ?? $normalized;
    }

    private function normalizeDeliveryMethodValue(string $method): string
    {
        $normalized = $this->normalizeComparableText($method);

        $map = [
            'standard' => 'standard',
            'štandardné' => 'standard',
            'standardne' => 'standard',
            'kuriér - slovenská pošta' => 'standard',
            'kurier - slovenska posta' => 'standard',
            'courier - slovak post' => 'standard',
            'express' => 'express',
            'expresné' => 'express',
            'expresne' => 'express',
            'pickup' => 'pickup',
            'osobný odber' => 'pickup',
            'osobny odber' => 'pickup',
            'personal pickup' => 'pickup',
        ];

        return $map[$normalized] ?? $normalized;
    }

    private function normalizeCountryValue(string $country): string
    {
        $normalized = $this->normalizeComparableText($country);

        $map = [
            'sk' => 'slovakia',
            'slovensko' => 'slovakia',
            'slovakia' => 'slovakia',
            'cz' => 'czechia',
            'česká republika' => 'czechia',
            'ceska republika' => 'czechia',
            'czech republic' => 'czechia',
            'czechia' => 'czechia',
            'pl' => 'poland',
            'poľsko' => 'poland',
            'polsko' => 'poland',
            'poland' => 'poland',
            'hu' => 'hungary',
            'maďarsko' => 'hungary',
            'madarsko' => 'hungary',
            'hungary' => 'hungary',
            'at' => 'austria',
            'rakusko' => 'austria',
            'rakúsko' => 'austria',
            'austria' => 'austria',
            'de' => 'germany',
            'nemecko' => 'germany',
            'germany' => 'germany',
        ];

        return $map[$normalized] ?? $normalized;
    }

    private function normalizePhoneValue(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone)) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '421')) {
            return $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) >= 10) {
            return '421' . substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '421' . $digits;
        }

        return $digits;
    }

    private function normalizeAddressFieldValue(string $field, string $value): string
    {
        return match ($field) {
            'email' => $this->normalizeComparableText($value),
            'phone' => $this->normalizePhoneValue($value),
            'country' => $this->normalizeCountryValue($value),
            default => $this->normalizeComparableText($value),
        };
    }

    private function prepareVariantOptions($variantOptions): array
    {
        $options = is_array($variantOptions) ? $variantOptions : [];
        ksort($options);

        return $options;
    }

    private function canonicalizeVariantOptions(array $variantOptions): array
    {
        $canonical = [];

        foreach ($variantOptions as $key => $value) {
            $canonicalKey = $this->normalizeComparableText((string) $key);
            $canonicalValue = $this->normalizeComparableText((string) $value);
            $canonical[$canonicalKey] = $canonicalValue;
        }

        ksort($canonical);

        return $canonical;
    }

    private function formatVariantOptionsForEmail(array $variantOptions): string
    {
        if (empty($variantOptions)) {
            return (string) __('messages.email.order_confirmation.no_options');
        }

        $chunks = [];
        foreach ($variantOptions as $key => $value) {
            $chunks[] = ucfirst(trim((string) $key)) . ': ' . trim((string) $value);
        }

        return implode(', ', $chunks);
    }

    private function sendOrderUpdateEmail(Order $order, array $changeLines): void
    {
        $address = $this->normalizeOrderAddress($order->address);
        $email = $address['email'] ?? ($order->user?->email ?? null);

        if (!$email) {
            return;
        }

        $currentLocale = app()->getLocale();
        $targetLocale = $order->user?->language ?? $currentLocale;
        if (!in_array($targetLocale, ['sk', 'en'], true)) {
            $targetLocale = $currentLocale;
        }

        app()->setLocale($targetLocale);

        try {
            $order->loadMissing('items.product', 'user');

            $statusLabel = $this->formatOrderStatusLabel((string) $order->status);
            $deliveryLabel = $this->formatDeliveryMethodLabel((string) $order->delivery_method);
            $paymentLabel = $this->formatPaymentMethodLabel((string) $order->payment_method);

            $items = $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_title' => $item->product?->title ?? __('messages.email.order_updated.product_fallback', ['id' => $item->product_id]),
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'variant_options' => $item->variant_options,
                ];
            })->toArray();

            Mail::send('emails.order-updated', [
                'order' => $order,
                'address' => $address,
                'changeLines' => $changeLines,
                'statusLabel' => $statusLabel,
                'deliveryMethodLabel' => $deliveryLabel,
                'paymentMethodLabel' => $paymentLabel,
                'items' => $items,
            ], function ($message) use ($email, $order) {
                $message->to($email)
                    ->subject(__('messages.email.order_updated.subject', ['reference' => $order->reference]))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            \Log::warning('Failed to send order update email: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'email' => $email,
            ]);
        } finally {
            app()->setLocale($currentLocale);
        }
    }

    private function sendOrderDeletedEmail(Order $order): void
    {
        $address = $this->normalizeOrderAddress($order->address);
        $email = $address['email'] ?? ($order->user?->email ?? null);

        if (!$email) {
            return;
        }

        $currentLocale = app()->getLocale();
        $targetLocale = $order->user?->language ?? $currentLocale;
        if (!in_array($targetLocale, ['sk', 'en'], true)) {
            $targetLocale = $currentLocale;
        }

        app()->setLocale($targetLocale);

        try {
            $order->loadMissing('items.product', 'user');

            $statusLabel = $this->formatOrderStatusLabel((string) $order->status);
            $deliveryLabel = $this->formatDeliveryMethodLabel((string) $order->delivery_method);
            $paymentLabel = $this->formatPaymentMethodLabel((string) $order->payment_method);

            $items = $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_title' => $item->product?->title ?? __('messages.email.order_deleted.product_fallback', ['id' => $item->product_id]),
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'variant_options' => $item->variant_options,
                ];
            })->toArray();

            Mail::send('emails.order-deleted', [
                'order' => $order,
                'address' => $address,
                'statusLabel' => $statusLabel,
                'deliveryMethodLabel' => $deliveryLabel,
                'paymentMethodLabel' => $paymentLabel,
                'items' => $items,
            ], function ($message) use ($email, $order) {
                $message->to($email)
                    ->subject(__('messages.email.order_deleted.subject', ['reference' => $order->reference]))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            \Log::warning('Failed to send order deleted email: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'email' => $email,
            ]);
        } finally {
            app()->setLocale($currentLocale);
        }
    }

    private function formatOrderStatusLabel(string $status): string
    {
        $normalized = $this->normalizeOrderStatusValue($status);
        $statusLabels = trans('messages.email.order_confirmation.statuses');

        if (is_array($statusLabels) && isset($statusLabels[$normalized])) {
            return (string) $statusLabels[$normalized];
        }

        return trim($status) !== '' ? ucfirst(trim($status)) : '—';
    }

    private function formatDeliveryMethodLabel(string $method): string
    {
        $normalized = $this->normalizeDeliveryMethodValue($method);
        $labels = trans('messages.email.order_confirmation.delivery_methods');
        if (is_array($labels) && isset($labels[$normalized])) {
            return (string) $labels[$normalized];
        }

        return trim($method) !== '' ? ucfirst(trim($method)) : '—';
    }

    private function formatPaymentMethodLabel(string $method): string
    {
        $normalized = $this->normalizePaymentMethodValue($method);
        $labels = trans('messages.email.order_confirmation.payment_methods');
        if (is_array($labels) && isset($labels[$normalized])) {
            return (string) $labels[$normalized];
        }

        return trim($method) !== '' ? ucfirst(trim($method)) : '—';
    }
}
