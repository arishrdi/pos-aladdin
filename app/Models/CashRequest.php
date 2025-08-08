<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRequest extends Model
{
    protected $fillable = [
        'outlet_id',
        'requested_by',
        'type',
        'amount',
        'reason',
        'proof_files',
        'status',
        'processed_by',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'proof_files' => 'array',
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessor untuk mendapatkan URL lengkap proof files
    public function getProofFilesUrlsAttribute()
    {
        if (!$this->proof_files) {
            return [];
        }

        return collect($this->proof_files)->map(function ($file) {
            return asset('uploads/' . $file);
        })->toArray();
    }

    // Helper methods
    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function canBeRejected()
    {
        return $this->status === 'pending';
    }

    public function approve($approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $approver->id,
            'admin_notes' => $notes,
            'processed_at' => now(),
        ]);
    }

    public function reject($approver, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $approver->id,
            'admin_notes' => $notes,
            'processed_at' => now(),
        ]);
    }

    public function getTypeTextAttribute()
    {
        return $this->type === 'add' ? 'Tambah Kas' : 'Kurang Kas';
    }
}
