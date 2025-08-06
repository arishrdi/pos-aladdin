<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintTemplate extends Model
{
    protected $fillable = [
        'company_name',
        'outlet_id',
        'company_slogan',
        'footer_message',
        'logo'
    ];

    protected $appends = ['logo_url']; 

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('uploads/' . $this->logo) : null;
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
