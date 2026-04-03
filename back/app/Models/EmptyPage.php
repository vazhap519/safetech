<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmptyPage extends Model
{
    protected $fillable = [
        'socials',
        'title',
'description',
'coming_soon',
    ];

    protected $casts = [
        'socials' => 'array', // 🔥 ავტომატურად array იქნება
    ];
}
