<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_name',
        'box_image',
        'box_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'box_price' => 'decimal:2',
    ];

    // ✅ Many-to-many relation with Category
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_box');
    }
}
