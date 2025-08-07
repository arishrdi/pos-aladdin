<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'type',
        'trigger_type',
        'trigger_value',
        'product_id',
        'category_id',
        'outlet_id',
        'bonus_type',
        'bonus_product_id',
        'bonus_quantity',
        'bonus_value',
        'max_bonus_per_transaction',
        'max_usage_per_member',
        'valid_from',
        'valid_until',
        'requires_approval',
        'is_active',
        'conditions'
    ];

    protected $casts = [
        'trigger_value' => 'decimal:2',
        'bonus_quantity' => 'decimal:2',
        'bonus_value' => 'decimal:2',
        'max_bonus_per_transaction' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'conditions' => 'json'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function bonusProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bonus_product_id');
    }

    public function bonusTransactions(): HasMany
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query, $date = null)
    {
        $date = $date ?? now();
        return $query->where(function ($query) use ($date) {
            $query->where('valid_from', '<=', $date)
                  ->orWhereNull('valid_from');
        })->where(function ($query) use ($date) {
            $query->where('valid_until', '>=', $date)
                  ->orWhereNull('valid_until');
        });
    }

    public function scopeForOutlet($query, $outletId)
    {
        return $query->where(function ($query) use ($outletId) {
            $query->where('outlet_id', $outletId)
                  ->orWhereNull('outlet_id');
        });
    }

    public function scopeAutomatic($query)
    {
        return $query->where('type', 'automatic');
    }

    public function scopeManual($query)
    {
        return $query->where('type', 'manual');
    }

    // Helper methods
    public function isValidForDate($date = null): bool
    {
        $date = $date ?? now();
        
        if ($this->valid_from && $date < $this->valid_from) {
            return false;
        }
        
        if ($this->valid_until && $date > $this->valid_until) {
            return false;
        }
        
        return true;
    }

    public function canBeUsedInOutlet($outletId): bool
    {
        return $this->outlet_id === null || $this->outlet_id == $outletId;
    }

    public function getUsageCount($memberId = null): int
    {
        $query = $this->bonusTransactions()->where('status', 'approved');
        
        if ($memberId) {
            $query->where('member_id', $memberId);
        }
        
        return $query->count();
    }

    public function canBeUsedByMember($memberId): bool
    {
        if (!$this->max_usage_per_member) {
            return true;
        }
        
        $usageCount = $this->getUsageCount($memberId);
        return $usageCount < $this->max_usage_per_member;
    }

    public function calculateBonusValue($triggerAmount = null): float
    {
        switch ($this->bonus_type) {
            case 'product':
                // Untuk bonus produk, nilai = quantity * harga produk bonus
                if ($this->bonusProduct) {
                    return $this->bonus_quantity * $this->bonusProduct->price;
                }
                return 0;
                
            case 'discount_percentage':
                if ($triggerAmount && $this->bonus_value) {
                    return $triggerAmount * ($this->bonus_value / 100);
                }
                return 0;
                
            case 'discount_amount':
                return $this->bonus_value ?? 0;
                
            default:
                return 0;
        }
    }

    // Static helper methods
    public static function getActiveRulesForOutlet($outletId)
    {
        return static::active()
                     ->valid()
                     ->forOutlet($outletId)
                     ->with(['product', 'category', 'bonusProduct', 'outlet'])
                     ->get();
    }

    public static function getAutomaticRulesForOutlet($outletId)
    {
        return static::getActiveRulesForOutlet($outletId)
                     ->where('type', 'automatic');
    }

    public static function getManualRulesForOutlet($outletId)
    {
        return static::getActiveRulesForOutlet($outletId)
                     ->where('type', 'manual');
    }
}