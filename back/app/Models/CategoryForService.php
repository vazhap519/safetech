<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class CategoryForService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'noindex',
    ];

    protected $casts = [
        'seo_keywords' => 'array',
        'faq' => 'array',
        'schema' => 'array',
        'noindex' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /*
    |------------------------------------------------------------------
    | 🔗 RELATION
    |------------------------------------------------------------------
    */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_for_service_id');
    }


}
