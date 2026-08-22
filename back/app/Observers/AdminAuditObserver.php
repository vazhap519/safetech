<?php

namespace App\Observers;

use App\Models\AdminAudit;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminAuditObserver
{
    private const REDACTED = '[REDACTED]';

    /** @var array<int, string> */
    private const IGNORED_FIELDS = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Keep delete audit rows intentionally small. Content models can contain
     * several large JSON/text attributes and serializing the whole record made
     * destructive admin actions noticeably slower while adding little value to
     * the audit trail.
     *
     * @var array<int, string>
     */
    private const DELETION_AUDIT_FIELDS = [
        'id',
        'public_id',
        'name',
        'title',
        'email',
        'slug',
        'key',
        'group',
        'status',
        'estimate_number',
        'recipient_name',
        'project_id',
        'service_id',
        'category_id',
        'category_for_service_id',
    ];

    public function created(Model $model): void
    {
        $this->record($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = array_diff_key(
            $model->getChanges(),
            array_flip(self::IGNORED_FIELDS),
        );

        if ($changes === []) {
            return;
        }

        $oldValues = [];

        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $model->getRawOriginal($field);
        }

        $this->record($model, 'updated', $oldValues, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $this->deletionSnapshot($model), []);
    }

    /** @param array<string, mixed> $oldValues @param array<string, mixed> $newValues */
    private function record(Model $model, string $action, array $oldValues, array $newValues): void
    {
        $user = Auth::user();

        if (! $user?->is_admin || ! Schema::hasTable('admin_audits')) {
            return;
        }

        try {
            AdminAudit::query()->create([
                'user_id' => $user->getAuthIdentifier(),
                'action' => $action,
                'auditable_type' => $model::class,
                'auditable_id' => $model->getKey(),
                'label' => $this->modelLabel($model),
                'old_values' => $this->sanitize($model, $oldValues),
                'new_values' => $this->sanitize($model, $newValues),
                'ip_address' => app()->bound('request') ? request()->ip() : null,
                'user_agent' => app()->bound('request')
                    ? mb_substr((string) request()->userAgent(), 0, 1000)
                    : null,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Unable to write the admin audit log.', [
                'action' => $action,
                'model' => $model::class,
                'model_id' => $model->getKey(),
                'exception' => $exception::class,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function deletionSnapshot(Model $model): array
    {
        $attributes = $model->getAttributes();
        $snapshot = [];

        foreach (self::DELETION_AUDIT_FIELDS as $field) {
            if (array_key_exists($field, $attributes)) {
                $snapshot[$field] = $attributes[$field];
            }
        }

        $keyName = $model->getKeyName();

        if (! array_key_exists($keyName, $snapshot) && array_key_exists($keyName, $attributes)) {
            $snapshot[$keyName] = $attributes[$keyName];
        }

        $snapshot['_attribute_count'] = count($attributes);

        return $snapshot;
    }

    private function modelLabel(Model $model): string
    {
        foreach (['name', 'title', 'email', 'slug'] as $field) {
            if (filled($model->getAttribute($field))) {
                return mb_substr((string) $model->getAttribute($field), 0, 255);
            }
        }

        return class_basename($model).' #'.$model->getKey();
    }

    /** @param array<string, mixed> $values @return array<string, mixed> */
    private function sanitize(Model $model, array $values): array
    {
        $sanitized = [];

        foreach ($values as $field => $value) {
            if (in_array($field, self::IGNORED_FIELDS, true)) {
                continue;
            }

            if ($this->isSensitiveField($field)
                || ($model instanceof SiteSetting
                    && $field === 'value'
                    && $this->isSensitiveField((string) $model->key))) {
                $sanitized[$field] = self::REDACTED;

                continue;
            }

            $sanitized[$field] = $this->sanitizeValue($value);
        }

        return $sanitized;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $sanitized = [];

            foreach ($value as $key => $nestedValue) {
                $sanitized[$key] = is_string($key) && $this->isSensitiveField($key)
                    ? self::REDACTED
                    : $this->sanitizeValue($nestedValue);
            }

            return $sanitized;
        }

        if (is_string($value)) {
            return mb_substr($value, 0, 4000);
        }

        return $value;
    }

    private function isSensitiveField(string $field): bool
    {
        return (bool) preg_match(
            '/(?:password|secret|token|credential|recovery|authentication|private[_-]?key)/i',
            $field,
        );
    }
}
