<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{





    // protected $fillable = ['user_id', 'product_id', 'is_active'];








    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the wishlist item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product in the wishlist.
     */
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }




    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope to get active wishlist items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get wishlist items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}