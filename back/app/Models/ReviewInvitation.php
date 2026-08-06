<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReviewInvitation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'consented_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $invitation): void {
            if (filled($invitation->token)) {
                return;
            }

            $invitation->token = self::generateToken();
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(Testimonial::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereNull('submitted_at')
            ->where(function (Builder $expiry): void {
                $expiry
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function hasExpired(): bool
    {
        return $this->expires_at?->lessThanOrEqualTo(now()) ?? false;
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && $this->submitted_at === null
            && ! $this->hasExpired();
    }

    public function getPublicUrlAttribute(): string
    {
        $baseUrl = rtrim(
            (string) (config('app.frontend_url') ?: config('app.url')),
            '/',
        );

        return $baseUrl.'/review/'.rawurlencode((string) $this->token);
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (self::query()->where('token', $token)->exists());

        return $token;
    }
}
