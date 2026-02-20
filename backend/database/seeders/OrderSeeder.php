<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('sk_SK');
        
        $users = User::where('role', 'user')->get();
        $products = Product::get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $deliveryMethods = ['Kurier', 'Pošta SK', 'Osobný odber'];
        $paymentMethods = ['Platba kartou', 'Dobierka', 'Bankový prevod'];
        $statuses = ['čakajúce', 'Spracováva sa', 'V preprave', 'Doručené'];
        $cities = ['Bratislava', 'Košice', 'Banská Bystrica', 'Žilina', 'Nitra', 'Trnava', 'Martin', 'Prešov'];

        $now = Carbon::now();
        
        // Create 5 orders from last month (January 2026)
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $daysInLastMonth = $lastMonthStart->daysInMonth;
        
        for ($i = 0; $i < 5; $i++) {
            $randomDate = $lastMonthStart->copy()->addDays(rand(0, $daysInLastMonth - 1));
            
            $order = Order::create([
                'user_id' => $users->random()->id,
                'reference' => 'ORD-' . $randomDate->format('Ym') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'delivery_method' => $deliveryMethods[array_rand($deliveryMethods)],
                'address' => json_encode([
                    'fullName' => $faker->name(),
                    'street' => $faker->streetAddress(),
                    'zip' => $faker->postcode(),
                    'city' => $cities[array_rand($cities)],
                    'country' => 'Slovensko',
                    'phone' => $faker->phoneNumber(),
                    'email' => $faker->email()
                ]),
                'phone' => $faker->phoneNumber(),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_token' => Str::random(32),
                'total' => 0,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $randomDate->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                'updated_at' => $randomDate->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
            ]);

            // Add random items to order (2-5 items)
            $itemCount = rand(2, 5);
            $orderTotal = 0;
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $itemPrice = $product->price ?? rand(10, 200);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                ]);
                
                $orderTotal += $quantity * $itemPrice;
            }
            
            // Update order total
            $order->update(['total' => $orderTotal]);
        }

        // Create 10 orders from this month (February 2026)
        $currentMonthStart = $now->copy()->startOfMonth();
        $daysElapsedInCurrentMonth = $now->day - 1; // 0-based: today is day 20, so 19 days elapsed
        $daysInCurrentMonth = $now->daysInMonth;
        
        for ($i = 0; $i < 10; $i++) {
            // Create orders from start of current month up to today
            $randomDate = $currentMonthStart->copy()->addDays(rand(0, $daysElapsedInCurrentMonth));
            
            $order = Order::create([
                'user_id' => $users->random()->id,
                'reference' => 'ORD-' . $randomDate->format('Ym') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'delivery_method' => $deliveryMethods[array_rand($deliveryMethods)],
                'address' => json_encode([
                    'fullName' => $faker->name(),
                    'street' => $faker->streetAddress(),
                    'zip' => $faker->postcode(),
                    'city' => $cities[array_rand($cities)],
                    'country' => 'Slovensko',
                    'phone' => $faker->phoneNumber(),
                    'email' => $faker->email()
                ]),
                'phone' => $faker->phoneNumber(),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_token' => Str::random(32),
                'total' => 0,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $randomDate->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                'updated_at' => $randomDate->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
            ]);

            // Add random items to order (2-5 items)
            $itemCount = rand(2, 5);
            $orderTotal = 0;
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $itemPrice = $product->price ?? rand(10, 200);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                ]);
                
                $orderTotal += $quantity * $itemPrice;
            }
            
            // Update order total
            $order->update(['total' => $orderTotal]);
        }
    }
}
