<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity', 'variant_options'];

    protected $casts = [
        'variant_options' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the price of this cart item including variant modifiers
     * Returns base product price + variant price modifiers
     */
    public function getVariantPrice(): float
    {
        $basePrice = $this->product->calculated_price ?? $this->product->price;
        $modifier = 0;

        if ($this->variant_options && $this->product->variant_pricing) {
            foreach ($this->variant_options as $variantType => $selectedValue) {
                if (isset($this->product->variant_pricing[$variantType][$selectedValue])) {
                    $modifier += $this->product->variant_pricing[$variantType][$selectedValue];
                }
            }
        }

        return $basePrice + $modifier;
    }
}
