<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'otp',
        'is_verified',
        'email_verified_at',
        'profile_image',
        'address',
        'city',
        'state',
        'pincode',
        'date_of_birth',
        'gender',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's active orders
     */
    public function activeOrders()
    {
        return $this->orders()->where('status', '!=', 'cancelled');
    }

    /**
     * Check if user is verified
     */
    public function isVerified()
    {
        return $this->is_verified == 1;
    }

    /**
     * Get user's full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->pincode
        ]);
        
        return implode(', ', $parts);
    }
}
