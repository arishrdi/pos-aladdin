<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBalanceSnapshot extends Model
{
    protected $fillable = [
        'outlet_id',
        'date',
        'opening_balance',
        'closing_balance',
        'total_sales_cash',
        'total_sales_other',
        'manual_additions',
        'manual_subtractions',
        'refunds',
        'transactions_count',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales_cash' => 'decimal:2',
        'total_sales_other' => 'decimal:2',
        'manual_additions' => 'decimal:2',
        'manual_subtractions' => 'decimal:2',
        'refunds' => 'decimal:2',
        'transactions_count' => 'integer'
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor untuk net change
    public function getNetChangeAttribute(): float
    {
        return $this->closing_balance - $this->opening_balance;
    }

    // Accessor untuk total sales
    public function getTotalSalesAttribute(): float
    {
        return $this->total_sales_cash + $this->total_sales_other;
    }

    // Scope untuk filter by outlet
    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Scope untuk filter by date range
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Helper method untuk format currency
    public function formatCurrency($field): string
    {
        $value = $this->$field;
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
