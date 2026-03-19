<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Subscription extends Model
{
    use HasFactory;

    // --- CORRECTION 1: Use the correct table name from the database ---
    // The actual database table is 'subscriptions', not 'subscription_admin_products'.
    protected $table = 'subscriptions';

    protected $fillable = [
        'title',          // Changed 'plan_name' to 'title' (based on schema list)
        'description',    // Added 'description'
        'valid_days',     // Kept 'valid_days'
        'price',          // Changed 'amount' to 'price' (based on schema list)
        'image',          // Kept 'image'
        'status',       // Changed 'is_active' to 'status' (based on schema list)
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',// Using 'price' to match the column list
        // Assuming 'status' column stores an integer/string, not a boolean,
        // or you can map it if you know the status values (e.g., 1 for active).
        // Let's stick to the visible column name.
    ];

    /**
     * Helper to get the display image URL.
     * Use this instead of direct property access when showing images.
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    // You can keep this function, but ensure the column is named 'status' in the DB.
    public function isActive()
    {
        // Assuming 'status' is a numerical field where '1' means active.
        return $this->status == 1;
    }

    // Boot method to handle model events (remains the same and is good practice)
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subscription) {
            // Delete image file if exists
            if ($subscription->image && Storage::disk('public')->exists($subscription->image)) {
                Storage::disk('public')->delete($subscription->image);
            }
        });
    }
}