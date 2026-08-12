<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class AiMessage extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tool_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AiMessage $message): void {
            if (blank($message->public_id)) {
                $message->public_id = (string) Str::uuid();
            }
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(AiFeedback::class);
    }
}
