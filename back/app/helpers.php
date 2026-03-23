<?php

use App\Models\Settings;

function settings()
{
    return cache()->remember('settings', now()->addMinutes(10), function () {
        return \App\Models\Settings::firstOrCreate([]);
    });
}
if (!function_exists('geoToLatin')) {
    function geoToLatin($text)
    {
        $map = [
            'ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'t',
            'ი'=>'i','კ'=>'k','ლ'=>'l','მ'=>'m','ნ'=>'n','ო'=>'o','პ'=>'p','ჟ'=>'zh',
            'რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'f','ქ'=>'q','ღ'=>'gh','ყ'=>'y',
            'შ'=>'sh','ჩ'=>'ch','ც'=>'ts','ძ'=>'dz','წ'=>'w','ჭ'=>'ch','ხ'=>'kh','ჯ'=>'j','ჰ'=>'h',
        ];

        return strtr($text, $map);
    }
}
