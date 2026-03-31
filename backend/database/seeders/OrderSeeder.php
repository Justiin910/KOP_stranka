<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('sk_SK');

        $users = User::where('role', 'user')->get();
        $products = Product::query()->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $productsWithVariants = $products
            ->filter(function (Product $product) {
                $variants = $product->variants;
                if (is_string($variants)) {
                    $variants = json_decode($variants, true);
                }

                return is_array($variants) && count($variants) > 0;
            })
            ->values();

        $deliveryMethods = ['standard', 'express', 'pickup'];
        $paymentMethods = ['card', 'googlepay', 'paypal'];
        $statuses = ['pending', 'paid', 'cancelled', 'shipped', 'delivered'];
        $cities = ['Bratislava', 'Košice', 'Banská Bystrica', 'Žilina', 'Nitra', 'Trnava', 'Martin', 'Prešov'];

        $now = Carbon::now();

        $createOrder = function (Carbon $randomDate) use (
            $users,
            $products,
            $productsWithVariants,
            $deliveryMethods,
            $paymentMethods,
            $statuses,
            $cities,
            $faker
        ) {
            $user = $users->random();
            $localPhone = sprintf('%03d %03d %03d', rand(900, 999), rand(100, 999), rand(100, 999));
            $fullPhone = '+421 ' . $localPhone;
            $timestamp = $randomDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            $order = Order::create([
                'user_id' => $user->id,
                'reference' => 'ORD-' . $timestamp->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'delivery_method' => $deliveryMethods[array_rand($deliveryMethods)],
                'address' => json_encode([
                    'fullName' => $user->name,
                    'street' => $faker->streetAddress(),
                    'zip' => $faker->postcode(),
                    'city' => $cities[array_rand($cities)],
                    'country' => 'Slovensko',
                    'phone' => $fullPhone,
                    'email' => $user->email,
                ]),
                'phone' => $fullPhone,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_token' => Str::random(32),
                'total' => 0,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $itemCount = rand(2, 5);
            $orderTotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = ($j === 0 && $productsWithVariants->isNotEmpty())
                    ? $productsWithVariants->random()
                    : $products->random();

                $quantity = rand(1, 5);
                $variantOptions = $this->buildVariantOptions($product->variants);
                $itemPrice = (float) ($product->price ?? rand(10, 200));
                $itemPrice += $this->resolveVariantPriceModifier($product->variant_pricing, $variantOptions);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'variant_options' => $variantOptions,
                ]);

                $orderTotal += $quantity * $itemPrice;
            }

            $order->update(['total' => $orderTotal]);
        };

        // Create 8 orders from last month.
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $daysInLastMonth = $lastMonthStart->daysInMonth;

        for ($i = 0; $i < 8; $i++) {
            $randomDate = $lastMonthStart->copy()->addDays(rand(0, $daysInLastMonth - 1));
            $createOrder($randomDate);
        }

        // Create 12 orders from this month.
        $currentMonthStart = $now->copy()->startOfMonth();
        $daysElapsedInCurrentMonth = max(0, $now->day - 1);

        for ($i = 0; $i < 12; $i++) {
            $randomDate = $currentMonthStart->copy()->addDays(rand(0, $daysElapsedInCurrentMonth));
            $createOrder($randomDate);
        }
    }

    private function buildVariantOptions(mixed $variantsRaw): ?array
    {
        $variants = $variantsRaw;

        if (is_string($variants)) {
            $variants = json_decode($variants, true);
        }

        if (!is_array($variants) || count($variants) === 0) {
            return null;
        }

        $selected = [];
        foreach ($variants as $type => $options) {
            if (!is_array($options) || count($options) === 0) {
                continue;
            }

            $selected[$type] = $options[array_rand($options)];
        }

        return count($selected) > 0 ? $selected : null;
    }

    private function resolveVariantPriceModifier(mixed $variantPricingRaw, ?array $variantOptions): float
    {
        if (!$variantOptions) {
            return 0.0;
        }

        $variantPricing = $variantPricingRaw;
        if (is_string($variantPricing)) {
            $variantPricing = json_decode($variantPricing, true);
        }

        if (!is_array($variantPricing)) {
            return 0.0;
        }

        $modifier = 0.0;
        foreach ($variantOptions as $type => $option) {
            $modifier += (float) ($variantPricing[$type][$option] ?? 0);
        }

        return $modifier;
    }
}
