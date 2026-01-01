<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['title', 'brand', 'price', 'oldPrice', 'image', 'rating', 'reviews', 'category', 'stock', 'monthlyPrice', 'description', 'service', 'topBadge', 'addedAt'];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}

