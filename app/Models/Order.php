<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'order_code',
        'order_id',               // ← new
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_pincode',
        'subtotal',
        'delivery_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'order_notes',
        'number_of_boxes',
        'receiver_name',
        'receiver_phone',
        'delivery_date',
        'delivered_at',
        'latitude',
        'longitude',
        'delivery_time',
        'cart_data',              // JSON field
        'address_details',
        'house_block',
        'area_road',
        'save_as',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_date' => 'datetime',
        'delivered_at' => 'datetime',
        'cart_data' => 'array',    // automatically JSON decode/encode
    ];

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation to Customer (optional)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relation to OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Alias
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
