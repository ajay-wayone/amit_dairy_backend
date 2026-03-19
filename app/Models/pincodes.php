<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class pincodes extends Model
{
    protected $table = 'pincodes';

    protected $fillable = [
        'address',
        'pincode',
        'status'
    ];
}
