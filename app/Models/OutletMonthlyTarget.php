<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutletMonthlyTarget extends Model
{
    protected $fillable = [
        'outlet_id',
        'month',
        'target_amount',
    ];

    protected $casts = [
        'month' => 'integer',
        'target_amount' => 'decimal:2',
    ];

    // Nama bulan dalam Bahasa Indonesia
    protected static $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Relationship to Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get month name in Indonesian
     */
    public function getMonthNameAttribute()
    {
        return self::$monthNames[$this->month] ?? null;
    }

    /**
     * Validation rules
     */
    public static function rules()
    {
        return [
            'month' => 'required|integer|min:1|max:12',
            'target_amount' => 'required|numeric|min:0',
        ];
    }
}
