<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    // ✅ Fields jo mass-assignable hain
    protected $fillable = [
        'box_name',
        'box_image',
        'box_price',
        'is_active',
    ];

    // ✅ Type casting for automatic conversion
    protected $casts = [
        'is_active' => 'boolean',
        'box_price' => 'decimal:2',
    ];

    // ❌ Optional: isActive() method needed only if you want to use it like $box->isActive()
    public function isActive()
    {
        return $this->is_active;
    }
}
