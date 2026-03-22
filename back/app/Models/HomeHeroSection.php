<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeHeroSection extends Model  implements HasMedia
{
    use InteractsWithMedia;
        protected $fillable=[
        'home_hero_title',
        'home_hero_description',
        'home_hero_list',
        'home_hero_call_button_text',
        'home_hero_call_button_number',
        'home_hero_service_button_text'
    ];
    protected $casts = [
    'home_hero_list' => 'array',
];

 public function registerMediaConversions(Media $media = null): void
    {
        $this
             ->addMediaConversion('webp')
        ->format('webp') // 🔥 ეს არის სწორი ახლა
        ->quality(80)
        ->nonQueued();
    }
    public static function formatPhone($value)
{
    $digits = preg_replace('/\D/', '', $value);

    return preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $digits);
}
}
