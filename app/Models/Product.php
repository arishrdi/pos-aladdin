<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'sku',
        'barcode', // Tambahkan barcode ke fillable
        'description',
        'category_id',
        'price',
        'image',
        'is_active',
        'unit'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'barcode_image_url']; // Tambahkan barcode_image_url

    // Accessor untuk image_url
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('uploads/' . $this->image) : null;
    }

    // Accessor untuk barcode image (opsional)
    public function getBarcodeImageUrlAttribute()
    {
        if ($this->barcode) {
            // Jika menggunakan package barcode generator
            // return route('barcode.generate', ['code' => $this->barcode]);
            return null; // Sesuaikan dengan implementasi Anda
        }
        return null;
    }

    // Scope untuk mencari berdasarkan barcode
    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    // ... (method-method relasi yang sudah ada tetap dipertahankan)
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'inventories')
            ->withPivot(['quantity', 'min_stock']);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function inventoryHistory()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}