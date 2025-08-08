<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterTransaction extends Model
{
    protected $fillable = [
        'cash_register_id',
        'amount',
        'type',
        'description',
        'shift_id', 
        'user_id',
        'reason',
        'source',
        'proof_files',
    ];

    protected $casts = [
        'proof_files' => 'array',
    ];

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

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    
}
