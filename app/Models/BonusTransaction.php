<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BonusTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_number',
        'order_id',
        'bonus_rule_id',
        'outlet_id',
        'member_id',
        'cashier_id',
        'authorized_by',
        'type',
        'status',
        'total_value',
        'total_items',
        'reason',
        'notes',
        'conditions_met',
        'approved_at',
        'approved_by',
        'approval_notes',
        'used_at',
        'expired_at'
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'conditions_met' => 'json',
        'approved_at' => 'datetime',
        'used_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    // Boot method untuk generate bonus number otomatis
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->bonus_number) {
                $model->bonus_number = $model->generateBonusNumber();
            }
        });
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bonusRule(): BelongsTo
    {
        return $this->belongsTo(BonusRule::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bonusItems(): HasMany
    {
        return $this->hasMany(BonusItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('type', 'automatic');
    }

    public function scopeManual($query)
    {
        return $query->where('type', 'manual');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
        });
    }

    // Helper methods
    public function generateBonusNumber(): string
    {
        $prefix = 'BNS';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        return $prefix . $date . $random;
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
        return $this->status === 'approved' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expired_at && now() > $this->expired_at;
    }

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

    public function approve(User $approver, string $notes = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'approval_notes' => $notes
        ]);

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'approval_notes' => $reason
        ]);

        return true;
    }

    public function markAsUsed(): bool
    {
        if (!$this->canBeUsed()) {
            return false;
        }

        $this->update([
            'status' => 'used',
            'used_at' => now()
        ]);

        return true;
    }

    public function calculateTotalValue(): float
    {
        return $this->bonusItems()->sum('bonus_value');
    }

    public function updateTotals(): void
    {
        $this->update([
            'total_value' => $this->calculateTotalValue(),
            'total_items' => $this->bonusItems()->count()
        ]);
    }

    // Static methods
    public static function createFromRule(
        BonusRule $rule, 
        int $outletId, 
        int $cashierId, 
        array $items = [],
        int $memberId = null,
        Order $order = null,
        int $authorizedBy = null,
        string $reason = null
    ): self {
        $transaction = static::create([
            'order_id' => $order?->id,
            'bonus_rule_id' => $rule->id,
            'outlet_id' => $outletId,
            'member_id' => $memberId,
            'cashier_id' => $cashierId,
            'authorized_by' => $authorizedBy,
            'type' => $rule->type,
            'status' => $rule->requires_approval ? 'pending' : 'approved',
            'reason' => $reason,
            'expired_at' => $rule->valid_until
        ]);

        // Add bonus items
        foreach ($items as $item) {
            $transaction->bonusItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product_price' => $item['product_price'] ?? 0,
                'bonus_value' => $item['bonus_value'] ?? 0,
                'notes' => $item['notes'] ?? null,
                'status' => $rule->requires_approval ? 'pending' : 'approved'
            ]);
        }

        // Update totals
        $transaction->updateTotals();

        return $transaction;
    }

    public static function getPendingForOutlet(int $outletId)
    {
        return static::pending()
                     ->forOutlet($outletId)
                     ->with(['bonusRule', 'member', 'cashier', 'bonusItems.product'])
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    public static function getHistoryForOutlet(int $outletId, $dateFrom = null, $dateTo = null)
    {
        $query = static::forOutlet($outletId)
                       ->with(['bonusRule', 'member', 'cashier', 'approvedBy', 'bonusItems.product'])
                       ->orderBy('created_at', 'desc');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->get();
    }
}