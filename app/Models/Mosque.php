<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mosque extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'outlet_id'];

    protected $dates = ['deleted_at'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
