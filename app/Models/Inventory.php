<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'outlet_id',
        'min_stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function inventoryHistories()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function lastStock()
    {
        return $this->hasOne(InventoryHistory::class, 'product_id', 'product_id')
            ->join('inventories', function ($join) {
                $join->on('inventories.product_id', '=', 'inventory_histories.product_id')
                    ->on('inventories.outlet_id', '=', 'inventory_histories.outlet_id');
            })
            ->latest('inventory_histories.created_at');
    }
    
    public function stockByType()
    {
        $subQuery = DB::table('inventory_histories')
            ->select('product_id', 'outlet_id', 'type', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('product_id', 'outlet_id', 'type');

        return $this->hasMany(InventoryHistory::class, 'product_id', 'product_id')
            ->joinSub($subQuery, 'latest', function($join) {
                $join->on('inventory_histories.product_id', '=', 'latest.product_id')
                    ->on('inventory_histories.outlet_id', '=', 'latest.outlet_id')
                    ->on('inventory_histories.type', '=', 'latest.type')
                    ->on('inventory_histories.created_at', '=', 'latest.max_created_at');
            })
            ->orderBy('inventory_histories.created_at', 'desc');
    }
}
