<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSection extends Model
{
    protected $fillable = [
        'contact_page_number',
        'contact_page_why_title',
        'contact_page_why_text',
        'contact_page_hero_title',
        'contact_page_hero_text',

//        'contact_page_cta_title',
        'contact_page_info_title',

        'contact_page_whatsapp',
        'contact_page_viber',
        'contact_page_email',
        'contact_page_address',

    ];
    protected $casts = [
        'contact_page_why_text'=> 'array',

    ];
}
