<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'outlet_id',
        'user_id',
        'shift_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'total_paid',
        'change',
        'payment_method',
        'status',
        'notes',
        'member_id',
        'approval_status',
        'payment_proof',
        'approval_notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'cancellation_status',
        'cancellation_reason',
        'cancellation_notes',
        'cancellation_requested_by',
        'cancellation_requested_at',
        'cancellation_processed_by',
        'cancellation_processed_at',
        'cancellation_admin_notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
        'cancellation_processed_at' => 'datetime'
    ];

    protected $appends = ['payment_proof_url'];

    public function scopeMonthlyTotal($query, $month = null, $outletId = null)
    {
        $date = $month ? Carbon::parse($month) : Carbon::now();

        $query = $query->whereBetween('created_at', [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString()
        ]);

        $query->where('status', 'completed');

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        // if ($outletId) {
        //     $query->where('outlet_id', $outletId);
        // }

        return $query->sum('total');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function inventoryHistory()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    // Bonus relationships
    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    // Approval relationships
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Cancellation relationships
    public function cancellationRequester()
    {
        return $this->belongsTo(User::class, 'cancellation_requested_by');
    }

    public function cancellationProcessor()
    {
        return $this->belongsTo(User::class, 'cancellation_processed_by');
    }

    // Scopes for approval status
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canBeApproved(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function approve(User $approver, string $notes = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
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
            'approval_status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason
        ]);

        return true;
    }

    // Cancellation helper methods
    public function canRequestCancellation(): bool
    {
        // Bisa request pembatalan jika status pending atau completed, dan belum pernah request cancellation
        return in_array($this->status, ['pending', 'completed']) && $this->cancellation_status === 'none';
    }

    public function isPendingCancellation(): bool
    {
        return $this->cancellation_status === 'requested';
    }

    public function isCancellationApproved(): bool
    {
        return $this->cancellation_status === 'approved';
    }

    public function isCancellationRejected(): bool
    {
        return $this->cancellation_status === 'rejected';
    }

    public function requestCancellation(User $requester, string $reason, string $notes = null): bool
    {
        if (!$this->canRequestCancellation()) {
            return false;
        }

        $this->update([
            'cancellation_status' => 'requested',
            'cancellation_reason' => $reason,
            'cancellation_notes' => $notes,
            'cancellation_requested_by' => $requester->id,
            'cancellation_requested_at' => now()
        ]);

        return true;
    }

    public function approveCancellation(User $approver, string $adminNotes = null): bool
    {
        if ($this->cancellation_status !== 'requested') {
            return false;
        }

        $this->update([
            'cancellation_status' => 'approved',
            'cancellation_processed_by' => $approver->id,
            'cancellation_processed_at' => now(),
            'cancellation_admin_notes' => $adminNotes,
            'status' => 'cancelled'
        ]);

        return true;
    }

    public function rejectCancellation(User $approver, string $adminNotes): bool
    {
        if ($this->cancellation_status !== 'requested') {
            return false;
        }

        $this->update([
            'cancellation_status' => 'rejected',
            'cancellation_processed_by' => $approver->id,
            'cancellation_processed_at' => now(),
            'cancellation_admin_notes' => $adminNotes
        ]);

        return true;
    }

    public function getCancellationTypeAttribute(): string
    {
        if ($this->status === 'pending') {
            return 'pembatalan';
        } elseif ($this->status === 'completed') {
            return 'refund';
        }
        return 'unknown';
    }

    // Accessor for payment proof URL (sama seperti Product model)
    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? asset('uploads/' . $this->payment_proof) : null;
    }
}
