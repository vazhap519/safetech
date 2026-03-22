<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeHowWork extends Model
{
    protected $fillable=[
        'title',
        'description',
        'how_cards'
    ];
    protected $casts=[
        'how_cards'=>'array',
    ];
}
