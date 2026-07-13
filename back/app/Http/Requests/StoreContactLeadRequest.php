<?php

namespace App\Http\Requests;

use App\Domain\Leads\Data\LeadData;
use Illuminate\Foundation\Http\FormRequest;

final class StoreContactLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $details = $this->input('details');

        $this->merge([
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'service_slug' => $this->input('service_slug', $this->input('serviceSlug')),
            'project_size' => $this->input(
                'project_size',
                $this->input('project-size', $this->input('projectSize')),
            ),
            'property_type' => $this->input(
                'property_type',
                $this->input('property-type', $this->input('propertyType')),
            ),
            'message' => $this->input(
                'message',
                is_string($details) ? $details : $this->input('details_message'),
            ),
            'details' => is_array($details) ? $details : [],
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'first_name' => ['nullable', 'string', 'max:60'],
            'last_name' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'required_without:email', 'string', 'regex:/^[+()0-9\s-]{7,24}$/'],
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:160'],
            'service' => ['nullable', 'string', 'max:120'],
            'service_slug' => ['nullable', 'string', 'max:120', 'exists:services,slug'],
            'project_size' => ['nullable', 'string', 'max:80'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'details' => ['nullable', 'array'],
            'details.*.key' => ['required_with:details', 'string', 'max:100'],
            'details.*.label' => ['required_with:details', 'string', 'max:160'],
            'details.*.type' => ['nullable', 'string', 'max:40'],
            'details.*.value' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:3000'],
            'source' => ['required', 'string', 'max:80'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function toData(): LeadData
    {
        $data = $this->validated();

        return new LeadData(
            name: $data['name'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            company: $data['company'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            service: $data['service'] ?? null,
            serviceSlug: $data['service_slug'] ?? null,
            projectSize: $data['project_size'] ?? null,
            propertyType: $data['property_type'] ?? null,
            details: $this->normalizeDetails($data['details'] ?? []),
            message: $data['message'] ?? null,
            source: $data['source'],
            ipHash: hash_hmac('sha256', (string) $this->ip(), (string) config('app.key')),
            userAgent: mb_substr((string) $this->userAgent(), 0, 500) ?: null,
        );
    }

    /** @param mixed $details */
    private function normalizeDetails(mixed $details): array
    {
        if (! is_array($details)) {
            return [];
        }

        return collect($details)
            ->filter(fn ($detail): bool => is_array($detail))
            ->map(function (array $detail): array {
                return [
                    'key' => trim((string) ($detail['key'] ?? '')),
                    'label' => trim((string) ($detail['label'] ?? '')),
                    'type' => trim((string) ($detail['type'] ?? '')),
                    'value' => trim((string) ($detail['value'] ?? '')),
                ];
            })
            ->filter(fn (array $detail): bool => $detail['key'] !== '' && $detail['label'] !== '' && $detail['value'] !== '')
            ->values()
            ->all();
    }

    public function messages(): array
    {
        return [
            'email.email' => 'ელფოსტის ფორმატი არასწორია.',
            'email.required_without' => 'მიუთითეთ ელფოსტა ან ტელეფონის ნომერი.',
            'phone.required_without' => 'მიუთითეთ ტელეფონის ნომერი ან ელფოსტა.',
            'phone.regex' => 'ტელეფონის ნომრის ფორმატი არასწორია.',
            'privacy.accepted' => 'მონაცემების დამუშავებაზე თანხმობა აუცილებელია.',
            'service_slug.exists' => 'არჩეული სერვისი ვერ მოიძებნა.',
            'details.array' => 'დამატებითი ველების ფორმატი არასწორია.',
            'website.max' => 'მოთხოვნა უარყოფილია.',
        ];
    }
}
