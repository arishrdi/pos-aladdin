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
        'remaining_balance',
        'change',
        'payment_method',
        'status',
        'notes',
        'member_id',
        'approval_status',
        'payment_proof',
        'transaction_category',
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
        'cancellation_admin_notes',
        'service_type',
        'installation_date',
        'installation_notes',
        'contract_pdf',
        'mosque_id',
        'leads_cabang_outlet_id',
        'deal_maker_outlet_id',
        'finance_approved_by',
        'finance_approved_at',
        'operational_approved_by',
        'operational_approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
        'cancellation_processed_at' => 'datetime',
        'installation_date' => 'date',
        'finance_approved_at' => 'datetime',
        'operational_approved_at' => 'datetime'
    ];

    protected $appends = ['payment_proof_url', 'contract_pdf_url'];

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

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }

    public function leadsCabangOutlet()
    {
        return $this->belongsTo(Outlet::class, 'leads_cabang_outlet_id');
    }

    public function dealMakerOutlet()
    {
        return $this->belongsTo(Outlet::class, 'deal_maker_outlet_id');
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

    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }

    public function operationalApprover()
    {
        return $this->belongsTo(User::class, 'operational_approved_by');
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

    // DP Settlement History relationship
    public function dpSettlementHistory()
    {
        return $this->hasMany(DpSettlementHistory::class);
    }

    // Transaction Edits relationship
    public function transactionEdits()
    {
        return $this->hasMany(TransactionEdit::class);
    }

    // Helper method to check if order can be edited
    public function canBeEdited(): bool
    {
        return TransactionEdit::canEditOrder($this);
    }

    // Get pending edit for this order
    public function getPendingEdit()
    {
        return $this->transactionEdits()->where('status', 'pending')->first();
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

    // Dual approval helper methods
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
        return !$this->isFinanceApproved();
    }

    public function canBeOperationalApproved(): bool
    {
        return !$this->isOperationalApproved();
    }

    public function approveFinance(User $approver): bool
    {
        if (!$this->canBeFinanceApproved()) {
            return false;
        }

        $updateData = [
            'finance_approved_by' => $approver->id,
            'finance_approved_at' => now()
        ];

        // Check if operational is already approved, if so, mark as completed
        if ($this->isOperationalApproved()) {
            $updateData['status'] = 'completed';
        }

        $this->update($updateData);

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

        // Check if finance is already approved, if so, mark as completed
        if ($this->isFinanceApproved()) {
            $updateData['status'] = 'completed';
        }

        $this->update($updateData);

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

    // Accessor for contract PDF URL
    public function getContractPdfUrlAttribute(): ?string
    {
        return $this->contract_pdf ? asset('uploads/' . $this->contract_pdf) : null;
    }

    // DP/Settlement Methods
    public function getRemainingBalance(): float
    {
        return (float) $this->remaining_balance;
    }

    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    public function needsSettlement(): bool
    {
        return $this->transaction_category === 'dp' && $this->remaining_balance > 0;
    }

    public function canSettle(): bool
    {
        return $this->needsSettlement() && in_array($this->status, ['pending', 'completed']);
    }

    public function settle(float $amount, array $data = []): bool
    {
        if (!$this->canSettle()) {
            return false;
        }

        if ($amount <= 0 || $amount > $this->remaining_balance) {
            return false;
        }

        $newTotalPaid = $this->total_paid + $amount;
        $newRemainingBalance = $this->total - $newTotalPaid;

        $updateData = [
            'total_paid' => $newTotalPaid,
            'remaining_balance' => max(0, $newRemainingBalance)
        ];

        // Jika fully paid, ubah category ke lunas
        if ($newRemainingBalance <= 0) {
            $updateData['transaction_category'] = 'lunas';
        }

        // Merge dengan data tambahan (payment proof, notes, dll)
        $updateData = array_merge($updateData, $data);

        $this->update($updateData);
        return true;
    }

    // Scopes untuk DP
    public function scopeWithBalance($query)
    {
        return $query->where('remaining_balance', '>', 0);
    }

    public function scopeDpPending($query)
    {
        return $query->where('transaction_category', 'dp')->where('remaining_balance', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('transaction_category', $category);
    }
}
