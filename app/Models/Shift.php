<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'outlet_id',
        'user_id',
        'start_time',
        'end_time',
        'starting_cash',
        'ending_cash',
        'expected_cash',
        'cash_difference',
        'notes',
        'is_closed',
        'closed_by',
        'closing_time',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
}
