<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCard extends Model
{
    protected $fillable = [
        'user_id',
        'cardholder_name',
        'card_number',
        'expiry_month',
        'expiry_year',
        'brand',
        'last4',
        'is_default',
    ];

    protected $casts = [
        'card_number' => 'encrypted',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toPublicArray(bool $includeFullNumber = false): array
    {
        $payload = [
            'id' => $this->id,
            'cardholder_name' => $this->cardholder_name,
            'brand' => $this->brand,
            'last4' => $this->last4,
            'expiry_month' => $this->expiry_month,
            'expiry_year' => $this->expiry_year,
            'masked_number' => '**** **** **** ' . $this->last4,
        ];

        if ($includeFullNumber) {
            $payload['card_number'] = $this->card_number;
        }

        return $payload;
    }
}
