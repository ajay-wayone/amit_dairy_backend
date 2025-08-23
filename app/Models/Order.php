<?php
namespace App\Models;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory; // import User model
use Illuminate\Database\Eloquent\Model;

// import OrderItem model

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',       // Use user_id instead of customer_id
        'customer_name', // You may keep these fields if you store customer info separately
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
        'delivery_date',
        'delivered_at',
        'number_of_boxes',
        'cart_data',
        'address_details',
        'house_block',
        'area_road',
        'save_as',
        'receiver_name',
        'receiver_phone',
        'latitude',
        'longitude',
        'delivery_time',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'delivery_date'   => 'datetime',
        'delivered_at'    => 'datetime',
        'cart_data'       => 'array', // Cast JSON to array
    ];

    // Relation to User model using user_id foreign key
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation to OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
