<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'atas_nama_bank',
        'nama_bank',
        'nomor_transaksi_bank',
        'is_active',
        'tax',
        'tax_type',
        'pkp_atas_nama_bank',
        'pkp_nama_bank',
        'pkp_nomor_transaksi_bank',
        'non_pkp_atas_nama_bank',
        'non_pkp_nama_bank',
        'non_pkp_nomor_transaksi_bank',
        'qris',
        'target_tahunan',
        'target_bulanan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tax_type' => 'string',
    ];

    protected $appends = ['qris_url']; 

    public function getQrisUrlAttribute()
    {
        return $this->qris ? asset('uploads/' . $this->qris) : null;
    }

    public function print() {
        return $this->hasOne(PrintTemplate::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Many-to-many relationship with supervisors
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'user_outlets')->where('role', 'supervisor');
    }

    public function cashRegisters()
    {
        return $this->hasOne(CashRegister::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'inventories')
    //         ->withPivot('quantity'); // Ambil kolom quantity dari tabel pivot
    // }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'inventories')
            ->withPivot(['quantity', 'min_stock']); // Sesuaikan dengan kolom di tabel pivot
    }
    
    public function kasir()
    {
        return $this->hasOne(User::class, 'outlet_id', 'id')->where('role', 'kasir');
    }

    public function manajer()
    {
        return $this->hasOne(User::class, 'outlet_id', 'id')->where('role', 'manajer');
    }
    public function supervisor()
    {
        return $this->hasMany(User::class, 'outlet_id', 'id')->where('role', 'supervisor');
    }

    // Bonus relationships
    public function bonusRules()
    {
        return $this->hasMany(BonusRule::class);
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function getTaxRateAttribute()
    {
        return $this->tax_type === 'pkp' ? 11 : 0;
    }

    public function getCurrentBankingInfoAttribute()
    {
        if ($this->tax_type === 'pkp') {
            return [
                'atas_nama_bank' => $this->pkp_atas_nama_bank,
                'nama_bank' => $this->pkp_nama_bank,
                'nomor_transaksi_bank' => $this->pkp_nomor_transaksi_bank,
                'tax_rate' => 11,
                'tax_type' => 'PKP'
            ];
        } else {
            return [
                'atas_nama_bank' => $this->non_pkp_atas_nama_bank,
                'nama_bank' => $this->non_pkp_nama_bank,
                'nomor_transaksi_bank' => $this->non_pkp_nomor_transaksi_bank,
                'tax_rate' => 0,
                'tax_type' => 'Non-PKP'
            ];
        }
    }

    public function getActiveBankAccountAttribute()
    {
        $bankingInfo = $this->getCurrentBankingInfo();
        
        return [
            'account_name' => $bankingInfo['atas_nama_bank'],
            'bank_name' => $bankingInfo['nama_bank'],
            'account_number' => $bankingInfo['nomor_transaksi_bank']
        ];
    }

    public function isPkpAttribute()
    {
        return $this->tax_type === 'pkp';
    }

    public function calculateTax($amount)
    {
        $taxRate = $this->getTaxRateAttribute();
        return ($amount * $taxRate) / 100;
    }

    public function calculateTotalWithTax($amount)
    {
        $tax = $this->calculateTax($amount);
        return $amount + $tax;
    }
}
