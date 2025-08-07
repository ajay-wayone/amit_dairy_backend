<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content',
        'is_active',
        'meta_title',
        'meta_description',
        'last_updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Policy types
    const TYPE_TERMS = 'terms';
    const TYPE_PRIVACY = 'privacy';
    const TYPE_REFUND = 'refund';
    const TYPE_RETURN = 'return';
    const TYPE_DISCLAIMER = 'disclaimer';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_CANCELLATION = 'cancellation';

    public static function getTypes()
    {
        return [
            self::TYPE_TERMS => 'Terms & Conditions',
            self::TYPE_PRIVACY => 'Privacy Policy',
            self::TYPE_REFUND => 'Refund Policy',
            self::TYPE_RETURN => 'Return Policy',
            self::TYPE_DISCLAIMER => 'Disclaimer',
            self::TYPE_SHIPPING => 'Shipping Policy',
            self::TYPE_CANCELLATION => 'Cancellation Policy'
        ];
    }

    public function getTypeLabelAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
