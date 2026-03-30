<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class CategoryForService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

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
