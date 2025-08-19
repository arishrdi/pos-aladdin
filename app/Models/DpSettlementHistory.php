<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DpSettlementHistory extends Model
{
    use SoftDeletes;

    protected $table = 'dp_settlement_history';

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_proof',
        'notes',
        'remaining_balance_before',
        'remaining_balance_after',
        'is_final_payment',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_balance_before' => 'decimal:2',
        'remaining_balance_after' => 'decimal:2',
        'is_final_payment' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke User yang memproses
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
