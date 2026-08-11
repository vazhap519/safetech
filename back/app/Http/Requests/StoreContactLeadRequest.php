<?php

namespace App\Http\Requests;

use App\Domain\Leads\Data\LeadData;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreContactLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $details = $this->input('details');
        $serviceSlug = $this->input('service_slug', $this->input('serviceSlug'));
        $normalizedServiceSlug = is_string($serviceSlug) ? trim($serviceSlug) : null;
        $publishedServiceName = $normalizedServiceSlug
            ? Service::query()
                ->where('slug', $normalizedServiceSlug)
                ->where('is_published', true)
                ->value('name')
            : null;

        $this->merge([
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'service' => $publishedServiceName ?: $this->input('service'),
            'service_slug' => $normalizedServiceSlug,
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
        $isConsultationPopup = $this->input('source') === 'consultation-popup';

        return [
            'name' => ['nullable', 'required_if:source,home-cta,contact-page', 'string', 'max:100'],
            'first_name' => ['nullable', 'required_if:source,consultation-popup', 'string', 'max:60'],
            'last_name' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^[+()0-9\s-]{7,24}$/'],
            'email' => [$isConsultationPopup ? 'nullable' : 'required', 'email:rfc', 'max:160'],
            'address' => [$isConsultationPopup ? 'nullable' : 'required', 'string', 'max:255'],
            'service' => ['nullable', 'required_if:source,home-cta', 'string', 'max:120'],
            'service_slug' => [
                'nullable',
                'required_if:source,contact-page',
                'string',
                'max:120',
                Rule::exists('services', 'slug')->where(
                    fn ($query) => $query->where('is_published', true),
                ),
            ],
            'project_size' => ['nullable', 'string', 'max:80'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'details' => ['nullable', 'array', 'max:50'],
            'details.*.key' => ['required_with:details', 'string', 'max:100'],
            'details.*.label' => ['required_with:details', 'string', 'max:160'],
            'details.*.type' => ['nullable', 'string', 'max:40'],
            'details.*.value' => ['nullable', 'string', 'max:500'],
            'message' => [$isConsultationPopup ? 'nullable' : 'required', 'string', 'max:3000'],
            'source' => ['required', 'string', 'max:80'],
            'locale' => ['nullable', 'string', 'in:ka,en,ru'],
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
            address: $data['address'] ?? null,
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
        return match ($this->input('locale')) {
            'en' => [
                'name.required_if' => 'Enter your full name.',
                'first_name.required_if' => 'Enter your first name.',
                'last_name.required_if' => 'Enter your last name.',
                'phone.required' => 'Enter your phone number.',
                'phone.regex' => 'Enter a valid phone number.',
                'email.required' => 'Enter your email address.',
                'email.email' => 'Enter a valid email address.',
                'address.required' => 'Enter the city or service address.',
                'service.required_if' => 'Select the service you need.',
                'service_slug.required_if' => 'Select a service.',
                'service_slug.exists' => 'The selected service is unavailable.',
                'message.required' => 'Describe what service you need.',
                'privacy.accepted' => 'Consent to data processing is required.',
                'details.array' => 'The additional field format is invalid.',
                'details.max' => 'Too many additional fields were submitted.',
                'website.max' => 'The request was rejected.',
            ],
            'ru' => [
                'name.required_if' => 'Укажите имя и фамилию.',
                'first_name.required_if' => 'Укажите имя.',
                'last_name.required_if' => 'Укажите фамилию.',
                'phone.required' => 'Укажите номер телефона.',
                'phone.regex' => 'Введите корректный номер телефона.',
                'email.required' => 'Укажите электронную почту.',
                'email.email' => 'Введите корректный адрес электронной почты.',
                'address.required' => 'Укажите город или адрес оказания услуги.',
                'service.required_if' => 'Выберите необходимую услугу.',
                'service_slug.required_if' => 'Выберите услугу.',
                'service_slug.exists' => 'Выбранная услуга недоступна.',
                'message.required' => 'Опишите, какая услуга вам нужна.',
                'privacy.accepted' => 'Необходимо согласие на обработку данных.',
                'details.array' => 'Неверный формат дополнительных полей.',
                'details.max' => 'Отправлено слишком много дополнительных полей.',
                'website.max' => 'Запрос отклонен.',
            ],
            default => [
                'name.required_if' => 'მიუთითეთ სახელი და გვარი.',
                'first_name.required_if' => 'მიუთითეთ სახელი.',
                'last_name.required_if' => 'მიუთითეთ გვარი.',
                'phone.required' => 'მიუთითეთ ტელეფონის ნომერი.',
                'phone.regex' => 'ტელეფონის ნომრის ფორმატი არასწორია.',
                'email.required' => 'მიუთითეთ ელფოსტა.',
                'email.email' => 'ელფოსტის ფორმატი არასწორია.',
                'address.required' => 'მიუთითეთ ქალაქი ან მომსახურების მისამართი.',
                'service.required_if' => 'აირჩიეთ რომელი მომსახურება გჭირდებათ.',
                'service_slug.required_if' => 'აირჩიეთ სერვისი.',
                'service_slug.exists' => 'არჩეული სერვისი მიუწვდომელია.',
                'message.required' => 'აღწერეთ რა მომსახურება გჭირდებათ.',
                'privacy.accepted' => 'მონაცემების დამუშავებაზე თანხმობა აუცილებელია.',
                'details.array' => 'დამატებითი ველების ფორმატი არასწორია.',
                'details.max' => 'დამატებითი ველების დასაშვები რაოდენობა გადაჭარბებულია.',
                'website.max' => 'მოთხოვნა უარყოფილია.',
            ],
        };
    }
}
