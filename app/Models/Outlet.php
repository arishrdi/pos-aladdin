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
        'qris',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    
    
    

}
