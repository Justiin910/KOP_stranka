<?php

use App\Models\CartItem;
use App\Models\User;

$user = User::first();
echo "Testing with user: " . $user->email . "\n\n";

try {
    // Clear existing cart
    CartItem::where('user_id', $user->id)->delete();
    
    // Add product 1 with storage 128GB
    $item1 = CartItem::create([
        'user_id' => $user->id,
        'product_id' => 1,
        'quantity' => 1,
        'variant_options' => ['storage' => '128GB']
    ]);
    echo "✓ Created item 1 (storage: 128GB): ID " . $item1->id . "\n";

    // Add same product with storage 256GB - should create new item
    $item2 = CartItem::create([
        'user_id' => $user->id,
        'product_id' => 1,
        'quantity' => 1,
        'variant_options' => ['storage' => '256GB']
    ]);
    echo "✓ Created item 2 (storage: 256GB): ID " . $item2->id . "\n";

    // Add same product with storage 512GB - should create new item
    $item3 = CartItem::create([
        'user_id' => $user->id,
        'product_id' => 1,
        'quantity' => 1,
        'variant_options' => ['storage' => '512GB']
    ]);
    echo "✓ Created item 3 (storage: 512GB): ID " . $item3->id . "\n";

    // Check cart
    $items = CartItem::where('user_id', $user->id)->where('product_id', 1)->get();
    echo "\n✓ SUCCESS: Total cart items for product 1: " . $items->count() . "\n";
    foreach ($items as $item) {
        echo "  - Item " . $item->id . ": qty=" . $item->quantity . ", variants=" . json_encode($item->variant_options) . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
