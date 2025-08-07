<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'discount',
        'is_bonus',
        'bonus_transaction_id',
        'bonus_item_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Bonus relationships
    public function bonusTransaction()
    {
        return $this->belongsTo(BonusTransaction::class);
    }

    public function bonusItem()
    {
        return $this->belongsTo(BonusItem::class);
    }

    // Scopes
    public function scopeBonus($query)
    {
        return $query->where('is_bonus', true);
    }

    public function scopeNonBonus($query)
    {
        return $query->where('is_bonus', false);
    }

    // Helper methods
    public function isBonus(): bool
    {
        return (bool) $this->is_bonus;
    }
}
