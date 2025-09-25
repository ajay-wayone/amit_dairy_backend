<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
use HasFactory;

protected $fillable = [
'user_id',
'house_no',
'apartment',
'area',
'save_as',
'receiver_name',
'receiver_phone',
];

// Relation with User
public function user()
{
return $this->belongsTo(User::class);
}
}