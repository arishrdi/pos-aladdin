<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransactionEdit extends Model
{
    protected $fillable = [
        'order_id',
        'requested_by',
        'edit_type',
        'original_data',
        'new_data',
        'reason',
        'notes',
        'total_difference',
        'status',
        'finance_approved_by',
        'finance_approved_at',
        'operational_approved_by',
        'operational_approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'applied_at'
    ];

    protected $casts = [
        'original_data' => 'array',
        'new_data' => 'array',
        'total_difference' => 'decimal:2',
        'finance_approved_at' => 'datetime',
        'operational_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'applied_at' => 'datetime'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }

    public function operationalApprover()
    {
        return $this->belongsTo(User::class, 'operational_approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
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

    // Approval status methods (konsisten dengan Order model)
    public function isFinanceApproved(): bool
    {
        return !is_null($this->finance_approved_by) && !is_null($this->finance_approved_at);
    }

    public function isOperationalApproved(): bool
    {
        return !is_null($this->operational_approved_by) && !is_null($this->operational_approved_at);
    }

    public function isFullyApproved(): bool
    {
        return $this->isFinanceApproved() && $this->isOperationalApproved();
    }

    public function isPartiallyApproved(): bool
    {
        return ($this->isFinanceApproved() && !$this->isOperationalApproved()) || 
               (!$this->isFinanceApproved() && $this->isOperationalApproved());
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

    public function getDualApprovalStatus(): string
    {
        if ($this->isFullyApproved()) {
            return 'fully_approved';
        } elseif ($this->isPartiallyApproved()) {
            return 'partially_approved';
        } else {
            return 'pending';
        }
    }

    public function canBeFinanceApproved(): bool
    {
        return $this->isPending() && !$this->isFinanceApproved();
    }

    public function canBeOperationalApproved(): bool
    {
        return $this->isPending() && !$this->isOperationalApproved();
    }

    // Approval methods
    public function approveFinance(User $approver): bool
    {
        if (!$this->canBeFinanceApproved()) {
            return false;
        }

        $updateData = [
            'finance_approved_by' => $approver->id,
            'finance_approved_at' => now()
        ];

        // Check if operational is already approved, if so, mark as approved
        if ($this->isOperationalApproved()) {
            $updateData['status'] = 'approved';
        }

        $this->update($updateData);

        // Apply edit if fully approved
        if ($this->isFullyApproved()) {
            $this->applyEdit();
        }

        return true;
    }

    public function approveOperational(User $approver): bool
    {
        if (!$this->canBeOperationalApproved()) {
            return false;
        }

        $updateData = [
            'operational_approved_by' => $approver->id,
            'operational_approved_at' => now()
        ];

        // Check if finance is already approved, if so, mark as approved
        if ($this->isFinanceApproved()) {
            $updateData['status'] = 'approved';
        }

        $this->update($updateData);

        // Apply edit if fully approved
        if ($this->isFullyApproved()) {
            $this->applyEdit();
        }

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejected_by' => $approver->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason
        ]);

        return true;
    }

    // Apply edit to order
    public function applyEdit(): bool
    {
        if (!$this->isFullyApproved() || $this->applied_at) {
            return false;
        }

        $order = $this->order;
        if (!$order) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Apply changes to order
            $newData = $this->new_data;
        
        // Update order totals
        if (isset($newData['subtotal'])) {
            $order->subtotal = $newData['subtotal'];
        }
        if (isset($newData['total'])) {
            $order->total = $newData['total'];
        }
        if (isset($newData['tax'])) {
            $order->tax = $newData['tax'];
        }
        if (isset($newData['discount'])) {
            $order->discount = $newData['discount'];
        }

        $order->save();

        // Update order items
        if (isset($newData['items'])) {
            // Delete existing items
            $order->items()->delete();
            
            // Create new items
            foreach ($newData['items'] as $itemData) {
                $quantity = $itemData['quantity'];
                $price = $itemData['price'];
                $discount = $itemData['discount'] ?? 0;
                $subtotal = ($quantity * $price) - $discount;
                
                $order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ]);
            }
        }

            // Mark as applied
            $this->update(['applied_at' => now()]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to apply transaction edit: ' . $e->getMessage(), [
                'transaction_edit_id' => $this->id,
                'order_id' => $this->order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Helper method to check if order can be edited
    public static function canEditOrder(Order $order): bool
    {
        // Check if order status allows editing
        if (!in_array($order->status, ['pending', 'completed'])) {
            return false;
        }

        // Check if there's no pending edit request
        $pendingEdit = self::where('order_id', $order->id)
            ->where('status', 'pending')
            ->exists();

        return !$pendingEdit;
    }
}
