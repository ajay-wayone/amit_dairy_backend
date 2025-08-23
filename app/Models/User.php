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
        'otp',              // OTP save karne ke liye
        'purpose',          // Signup, login, resetpassword, password
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
        'otp', // Agar API me OTP dikhana ho to yaha se hata sakte ho
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
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's active orders (not cancelled).
     */
    public function activeOrders()
    {
        return $this->orders()->where('status', '!=', 'cancelled');
    }

    /**
     * Get the user's cart items.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class)->where('is_active', true);
    }

    /**
     * Get the user's wishlist items.
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class)->where('is_active', true);
    }

    /**
     * Get the user's reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_active', true);
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified()
    {
        return $this->is_verified === true;
    }
}
