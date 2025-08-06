<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name','member_code', 'phone', 'email', 'address', 'gender'];
    
    public function orders() {
        return $this->hasMany(Order::class);
    }
}

