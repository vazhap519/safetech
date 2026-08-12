<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AiConversation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'privacy_accepted_at' => 'datetime',
            'last_message_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AiConversation $conversation): void {
            if (blank($conversation->public_id)) {
                $conversation->public_id = (string) Str::uuid();
            }
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class)->orderBy('id');
    }

    public function contactLead(): BelongsTo
    {
        return $this->belongsTo(ContactLead::class);
    }

    public function knowledgeCandidates(): HasMany
    {
        return $this->hasMany(AiKnowledgeCandidate::class);
    }
}
