<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['title', 'brand', 'price', 'oldPrice', 'image', 'rating', 'reviews', 'category', 'stock', 'monthlyPrice', 'description', 'service', 'topBadge', 'addedAt', 'specs', 'discount_type', 'discount_value'];

    protected $appends = ['calculated_price'];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate price after applying discount to oldPrice
     * If oldPrice and discount are set, calculates: oldPrice - discount = price
     * Otherwise returns the price column as-is
     */
    public function getCalculatedPriceAttribute()
    {
        // If no oldPrice or discount, return current price
        if (!$this->oldPrice || !$this->discount_value || !$this->discount_type) {
            return $this->price;
        }

        $discountedPrice = $this->oldPrice;
        
        if ($this->discount_type === 'percent') {
            $discountedPrice = $this->oldPrice - ($this->oldPrice * $this->discount_value / 100);
        } else { // fixed
            $discountedPrice = $this->oldPrice - $this->discount_value;
        }

        return round($discountedPrice, 2);
    }
}
