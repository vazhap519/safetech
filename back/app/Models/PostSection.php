<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'title',
        'content',
        'position', // ✅ FIXED (order → position)
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (BONUS - CLEAN ORDERING)
    |--------------------------------------------------------------------------
    */

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
