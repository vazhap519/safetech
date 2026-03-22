<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCtaSection extends Model
{
    protected $fillable=[
        'cta_title',
        'cta_title_hilight',
        'cta_description',
        'cta_phone_btn_number',
        'cta_phone_btn_text',
        'cta_message_button_text'
    ];
}
