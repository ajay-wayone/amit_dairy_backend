<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
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
        'delivery_date',
        'delivered_at',
        'number_of_boxes',
        'user_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
