<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'title',
        'comment',
        'is_approved',
        'is_active'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get active reviews.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get reviews for a specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to get reviews by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
} 