<?php

namespace App\Http\Requests;

use App\Models\AnalyticsEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_type' => [
                'required',
                'string',
                Rule::in([
                    AnalyticsEvent::TYPE_SERVICE_VIEW,
                    AnalyticsEvent::TYPE_WHATSAPP_CLICK,
                ]),
            ],
            'service_slug' => ['nullable', 'string', 'max:255', 'exists:services,slug'],
            'page_path' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', Rule::in(['ka', 'en', 'ru'])],
            'visitor_id' => ['nullable', 'string', 'max:100'],
            'meta' => ['nullable', 'array', 'max:10'],
            'meta.*' => [
                'nullable',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_scalar($value) || mb_strlen((string) $value) > 255) {
                        $fail('Analytics metadata must contain short scalar values.');
                    }
                },
            ],
        ];
    }
}
