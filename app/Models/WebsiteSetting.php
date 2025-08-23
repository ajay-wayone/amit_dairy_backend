<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'address',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'about_us',
    ];

}
