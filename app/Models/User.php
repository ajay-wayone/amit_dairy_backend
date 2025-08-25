<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'otp',
        'purpose',
        'is_verified',
        'email_verified_at',
        'profile_image',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
    ];

    /**
     * Relations
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders()
    {
        return $this->orders()->where('status', '!=', 'cancelled');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class)->where('is_active', true);
    }

    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class)->where('is_active', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_active', true);
    }

    /**
     * Helper
     */
    public function isVerified()
    {
        return $this->is_verified === true;
    }
}
