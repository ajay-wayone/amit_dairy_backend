<?php
namespace App\Models;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',       
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
    ];

    // Relation to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation to OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Alias relation
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
