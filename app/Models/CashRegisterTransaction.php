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
    ];

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
