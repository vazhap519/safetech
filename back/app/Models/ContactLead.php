<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'address',
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
        'submission_key',
        'submission_payload_hash',
    ];

    protected static function booted(): void
    {
        // Older production databases may still have a restrictive FK. Clearing
        // the optional relation first keeps Filament and direct model deletes
        // reliable even before a legacy constraint repair is deployed.
        static::deleting(function (ContactLead $lead): void {
            $lead->aiConversations()->update(['contact_lead_id' => null]);
        });
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'privacy_accepted_at' => 'datetime',
        ];
    }
}
