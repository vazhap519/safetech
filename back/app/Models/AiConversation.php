<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function transcript(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->messages()
                ->with('feedback')
                ->get()
                ->map(function (AiMessage $message): string {
                    $role = match ($message->role) {
                        'user' => 'მომხმარებელი',
                        'assistant' => 'SafeTech AI',
                        default => $message->role,
                    };
                    $rating = match ($message->feedback?->rating) {
                        1 => ' 👍',
                        -1 => ' 👎',
                        default => '',
                    };
                    $time = $message->created_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i:s') ?? '—';

                    return "[{$time}] {$role}{$rating}\n{$message->content}";
                })
                ->implode("\n\n"),
        );
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
