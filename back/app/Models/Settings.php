<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'share_title',
        'share_buttons',
//Footer
    'footer_description',
        'footer_copyright',
        'footer_contact_info',
        'footer_headers',
        'footer_socials'
    ];

    protected $casts = [
        'share_buttons' => 'array',
        'footer_contact_info'=> 'array',
        'footer_headers'=> 'array',
        'footer_socials'=> 'array',
    ];
}
