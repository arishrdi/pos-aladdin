<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_transaction_id',
        'product_id',
        'quantity',
        'product_price',
        'bonus_value',
        'notes',
        'status',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'product_price' => 'decimal:2',
        'bonus_value' => 'decimal:2',
        'metadata' => 'json'
    ];

    // Relationships
    public function bonusTransaction(): BelongsTo
    {
        return $this->belongsTo(BonusTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeUsed(): bool
    {
        return $this->status === 'approved';
    }

    public function approve(): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update(['status' => 'approved']);
        return true;
    }

    public function reject(): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->update(['status' => 'rejected']);
        return true;
    }

    public function markAsUsed(): bool
    {
        if (!$this->canBeUsed()) {
            return false;
        }

        $this->update(['status' => 'used']);
        return true;
    }

    public function calculateBonusValue(): float
    {
        return $this->quantity * $this->product_price;
    }

    public function updateBonusValue(): void
    {
        $this->update([
            'bonus_value' => $this->calculateBonusValue()
        ]);
    }

    // Accessories/mutators
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->product_price, 0, ',', '.');
    }

    public function getFormattedBonusValueAttribute(): string
    {
        return 'Rp ' . number_format($this->bonus_value, 0, ',', '.');
    }

    public function getProductNameAttribute(): string
    {
        return $this->product->name ?? 'Unknown Product';
    }

    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->product_price;
    }

    // Static helper methods
    public static function createForTransaction(
        BonusTransaction $transaction,
        int $productId,
        float $quantity,
        float $productPrice = null,
        string $notes = null,
        array $metadata = []
    ): self {
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception("Product not found: {$productId}");
        }

        $productPrice = $productPrice ?? $product->price;
        $bonusValue = $quantity * $productPrice;

        $item = static::create([
            'bonus_transaction_id' => $transaction->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'product_price' => $productPrice,
            'bonus_value' => $bonusValue,
            'notes' => $notes,
            'status' => $transaction->bonusRule?->requires_approval ? 'pending' : 'approved',
            'metadata' => $metadata
        ]);

        // Update transaction totals
        $transaction->updateTotals();

        return $item;
    }

    public static function getTotalQuantityForProduct(int $productId, array $statuses = ['approved', 'used'])
    {
        return static::where('product_id', $productId)
                     ->whereIn('status', $statuses)
                     ->sum('quantity');
    }

    public static function getTotalValueForTransaction(int $transactionId, array $statuses = ['approved', 'used'])
    {
        return static::where('bonus_transaction_id', $transactionId)
                     ->whereIn('status', $statuses)
                     ->sum('bonus_value');
    }

    // Boot method untuk auto-update transaction totals
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($model) {
            $model->bonusTransaction?->updateTotals();
        });
        
        static::deleted(function ($model) {
            $model->bonusTransaction?->updateTotals();
        });
    }
}