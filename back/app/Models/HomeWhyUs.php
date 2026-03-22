<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeWhyUs extends Model
{
    protected $fillable=[
        'why_us_title',
        'why_us_description',
        'why_us_items'
    ];
    protected $casts=[
        'why_us_items'=>'array'
    ];
}
