<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscription_admin_products';

    protected $fillable = [
        'plan_name',
        'valid_days',
        'amount',
        'is_active',
        'image',
        'description',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isActive()
    {
        return $this->is_active;
    }

    // Boot method to handle model events
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
