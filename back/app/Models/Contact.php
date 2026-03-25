<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = [
        'name',
        'phone',
        'message',
    ];
    protected $casts = [
        'is_read' => 'boolean',
    ];
}
