<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'share_title',
        'share_buttons',
/*
 * ============================================================================================
 *                      FOOTER
 * ============================================================================================
 */
        /*
 * ============================================================================================
 *                      FOOTER BRAND
 * ============================================================================================
 */
        'footer_brand_text',
        'footer_brand_soc',

        /*
 * ============================================================================================
 *                      FOOTER HEADERS
 * ============================================================================================
 */
        'footer_headers',
        /*
 * ============================================================================================
 *                      FOOTER CONTACT AREA
 * ============================================================================================
 */
        'footer_contact_area',
        /*
    * ============================================================================================
    *                      FOOTER COPYRIGHT
    * ============================================================================================
    */
        'footer_copyright_text',

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

        'seo',
    ];

    protected $casts = [
        'share_buttons' => 'array',
        'footer_brand_soc'=> 'array',
        'footer_headers'=> 'array',
        'footer_contact_area'=> 'array',
        'contact_page_why_text'=>'array',

        'seo'=> 'array',
    ];


}
