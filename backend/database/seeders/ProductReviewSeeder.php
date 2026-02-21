<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $ratings = [3.0, 3.5, 4.0, 4.5, 5.0];
            $comments = [
                'Skvelý produkt! Presiahol moje očakávania. Všetkým ho vrele odporúčam.',
                'Úžasná kvalita a rýchla dodávka. Veľmi spokojný s nákupom.',
                'Skvelý produkt, dobrá cena za peniaze. Kúpil by som znova.',
                'Veľmi dobrý, ale trochu sa líši od popisu. Stále spokojný.',
                'Priemerna produkt. Robí to, čo má.',
                'Dokonalý! Najlepší nákup som kedy urobil. Výnimočný zákaznícky servis.',
                'Dobrá kvalita. Cena je spravodlivá. Odporúčam.',
                'Fantastický! Viac ako som čakal. Stojí to za peniaze.',
                'Slušný produkt. Mohlo by to byť lepšie, ale je prijateľné.',
                'Spokojný s nákupom. Dobrý výkon.',
                'Absolútne brilantný! Nemám, čo by som na tom skritizoval.',
                'Veľmi spokojný. Odporúčam priateľom a rodine.',
                'Najvyššia kvalita! Stojí to za investíciu.',
                'Dobrý zážitok celkovo. Spokojný s mojím nákupom.',
                'Je to v poriadku. Nič špeciálne, ale funkčné.',
            ];

            // Get all products
            $products = Product::all();
            
            // Get all users except owner
            $users = User::where('role', '!=', 'owner')->get();

            // For each product, create 3-5 reviews in Slovak only
            foreach ($products as $product) {
                $reviewCount = rand(3, 5);
                
                for ($i = 0; $i < $reviewCount; $i++) {
                    $randomComment = $comments[array_rand($comments)];
                    $randomRating = $ratings[array_rand($ratings)];
                    $randomUser = $users->random();
                    
                    ProductReview::create([
                        'product_id' => $product->id,
                        'user_id' => $randomUser->id,
                        'rating' => $randomRating,
                        'comment' => $randomComment,
                    ]);
                }

                // Update the product's rating and reviews count
                $productReviews = $product->productReviews()->get();
                $avgRating = $productReviews->avg('rating');
                $reviewCount = $productReviews->count();

                $product->update([
                    'rating' => round($avgRating, 1),
                    'reviews' => $reviewCount,
                ]);
            }
        }
    }

