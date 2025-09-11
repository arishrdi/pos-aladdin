<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name','member_code', 'phone', 'email', 'address', 'gender', 'lead_id', 'lead_number', 'outlet_id'];
    
    public function orders() {
        return $this->hasMany(Order::class);
    }

    // Bonus relationships
    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }
    
    // Outlet relationship
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    
    // Check if member is from leads
    public function isFromLead()
    {
        return !is_null($this->lead_id);
    }
    
    // Get lead identifier
    public function getLeadIdentifier()
    {
        return $this->isFromLead() ? $this->lead_number : $this->member_code;
    }
}

