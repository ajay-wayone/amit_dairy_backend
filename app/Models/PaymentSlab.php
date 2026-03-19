<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSlab extends Model
{
    protected $fillable = [
        'min_km',
        'max_km',
        'advance_percentage',
        'status'
    ];
}
