<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqSection extends Model
{
    protected $fillable=[
        'title',
'description',
'faq',
    ];
    protected $casts=[
        'faq'=>'array'
    ];
}
