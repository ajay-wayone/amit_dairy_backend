<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

   protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'read_at'    => 'datetime',
    'replied_at' => 'datetime',
];


    public function isRead()
    {
        return $this->status === 'read';
    }

    public function isUnread()
    {
        return $this->status === 'unread';
    }
}
