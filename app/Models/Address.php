<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address'; // table ka exact name

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address_line',
        'city',
        'pincode',
    ];

    public $timestamps = false;




    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}