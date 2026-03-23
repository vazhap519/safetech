<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSection extends Model
{
    protected $fillable = [
        'service_section_title',
        'service_section_description',
    ];

}
