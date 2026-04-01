<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'product_image',
        'types',
        'weight',
        'weight_type',
        'min_order',
        'max_order',
        'best_seller',
        'specialities',
        'featured_type',
        'status',
        'tags',
        'sub_images',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'discount_price'   => 'decimal:2',
        'weight'           => 'decimal:2',
        'min_order'        => 'integer',
        'max_order'        => 'integer',
        'types'            => 'array',
        'best_seller'      => 'boolean',
        'specialities'     => 'boolean',
        'status'           => 'boolean',
        'sub_images'       => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
