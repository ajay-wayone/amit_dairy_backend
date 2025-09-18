<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'transaction_id',
        'payment_method',
        'amount',
        'status',
        'response',
    ];

    /**
     * Relation: Payment belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: Payment belongs to an Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
