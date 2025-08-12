<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'outlet_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Many-to-many relationship with outlets (for supervisors)
     */
    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'user_outlets');
    }

    /**
     * Get all outlets accessible by this user
     * - Admin: all outlets
     * - Supervisor: assigned outlets from pivot table 
     * - Kasir: only their assigned outlet
     */
    public function getAccessibleOutlets()
    {
        switch ($this->role) {
            case 'admin':
                return Outlet::all();
            case 'supervisor':
                return $this->outlets; // Many-to-many relationship
            case 'kasir':
                return $this->outlet ? collect([$this->outlet]) : collect([]);
            default:
                return collect([]);
        }
    }

    /**
     * Check if user has access to specific outlet
     */
    public function canAccessOutlet($outletId)
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        if ($this->role === 'supervisor') {
            return $this->outlets()->where('outlets.id', $outletId)->exists();
        }
        
        if ($this->role === 'kasir') {
            return $this->outlet_id == $outletId;
        }
        
        return false;
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function lastShift()
    {
        return $this->hasOne(Shift::class)->latestOfMany();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    public function inventoryHistories()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function cashRegisterTransactions()
    {
        return $this->hasMany(CashRegisterTransaction::class);
    }

    // Bonus relationships
    public function bonusTransactionsAsCashier()
    {
        return $this->hasMany(BonusTransaction::class, 'cashier_id');
    }

    public function bonusTransactionsAsApprover()
    {
        return $this->hasMany(BonusTransaction::class, 'approved_by');
    }

    public function bonusTransactionsAsRejector()
    {
        return $this->hasMany(BonusTransaction::class, 'rejected_by');
    }

    public function bonusTransactionsAsAuthorizer()
    {
        return $this->hasMany(BonusTransaction::class, 'authorized_by');
    }
}
