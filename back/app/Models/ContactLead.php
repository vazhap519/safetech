<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    use HasFactory;

    protected $attributes = ['status' => 'new'];

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'company',
        'phone',
        'email',
        'service',
        'service_slug',
        'project_size',
        'property_type',
        'details',
        'message',
        'source',
        'status',
        'ip_hash',
        'user_agent',
        'privacy_accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'privacy_accepted_at' => 'datetime',
        ];
    }
}
