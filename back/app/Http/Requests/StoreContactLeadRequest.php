<?php

namespace App\Http\Requests;

use App\Domain\Leads\Data\LeadData;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreContactLeadRequest extends FormRequest
{
    /** @var array<int, string> */
    private const PUBLIC_SOURCES = [
        'consultation-popup',
        'home-cta',
        'contact-page',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $details = $this->input('details');
        $serviceSlug = $this->normalizeSlug(
            $this->input('service_slug', $this->input('serviceSlug')),
        );
        $publishedServiceName = $serviceSlug
            ? Service::query()
                ->where('slug', $serviceSlug)
                ->where('is_published', true)
                ->value('name')
            : null;

        $this->merge([
            'name' => $this->normalizeText($this->input('name')),
            'first_name' => $this->normalizeText(
                $this->input('first_name', $this->input('firstName')),
            ),
            'last_name' => $this->normalizeText(
                $this->input('last_name', $this->input('lastName')),
            ),
            'company' => $this->normalizeText($this->input('company')),
            'phone' => $this->normalizeText($this->input('phone')),
            'email' => $this->normalizeEmail($this->input('email')),
            'address' => $this->normalizeText($this->input('address')),
            // Public callers cannot supply an arbitrary service name. It is derived
            // from the selected, published service slug instead.
            'service' => $publishedServiceName,
            'service_slug' => $serviceSlug,
            'project_size' => $this->normalizeText($this->input(
                'project_size',
                $this->input('project-size', $this->input('projectSize')),
            )),
            'property_type' => $this->normalizeText($this->input(
                'property_type',
                $this->input('property-type', $this->input('propertyType')),
            )),
            'message' => $this->normalizeMessage($this->input(
                'message',
                is_string($details) ? $details : $this->input('details_message'),
            )),
            'details' => $this->normalizeDetailInputs($details),
            'source' => $this->normalizeSlug($this->input('source')),
            'locale' => $this->normalizeSlug($this->input('locale')),
            'website' => $this->normalizeText($this->input('website')),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name' => ['required', 'string', 'min:2', 'max:60'],
            'company' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^[+()0-9\s-]{7,24}$/'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'address' => ['required', 'string', 'min:2', 'max:255'],
            'service' => ['nullable', 'string', 'max:120'],
            'service_slug' => [
                'required',
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
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'source' => ['required', 'string', Rule::in(self::PUBLIC_SOURCES)],
            'locale' => ['nullable', 'string', 'in:ka,en,ru'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function toData(): LeadData
    {
        $data = $this->validated();
        $fullName = trim($data['first_name'].' '.$data['last_name']);

        return new LeadData(
            name: $fullName,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            company: $data['company'] ?? null,
            phone: $data['phone'],
            email: $data['email'],
            address: $data['address'],
            service: $data['service'] ?? null,
            serviceSlug: $data['service_slug'],
            projectSize: $data['project_size'] ?? null,
            propertyType: $data['property_type'] ?? null,
            details: $this->normalizeDetails($data['details'] ?? []),
            message: $data['message'],
            source: $data['source'],
            ipHash: hash_hmac('sha256', (string) $this->ip(), (string) config('app.key')),
            userAgent: mb_substr((string) $this->userAgent(), 0, 500) ?: null,
        );
    }

    /** @return array<int, array<string, string>> */
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

    private function normalizeText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        return $value !== '' ? $value : null;
    }

    private function normalizeMessage(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = preg_replace('/\R/u', "\n", trim($value)) ?? '';
        $value = preg_replace('/[\t ]+/u', ' ', $value) ?? '';

        return $value !== '' ? $value : null;
    }

    private function normalizeEmail(mixed $value): ?string
    {
        $value = $this->normalizeText($value);

        return $value !== null ? mb_strtolower($value) : null;
    }

    private function normalizeSlug(mixed $value): ?string
    {
        $value = $this->normalizeText($value);

        return $value !== null ? mb_strtolower($value) : null;
    }

    /** @return array<int, array<string, ?string>> */
    private function normalizeDetailInputs(mixed $details): array
    {
        if (! is_array($details)) {
            return [];
        }

        return collect($details)
            ->filter(fn (mixed $detail): bool => is_array($detail))
            ->map(fn (array $detail): array => [
                'key' => $this->normalizeText($detail['key'] ?? null),
                'label' => $this->normalizeText($detail['label'] ?? null),
                'type' => $this->normalizeText($detail['type'] ?? null),
                'value' => $this->normalizeText($detail['value'] ?? null),
            ])
            ->values()
            ->all();
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return match ($this->input('locale')) {
            'en' => [
                'first_name.required' => 'Enter your first name.',
                'first_name.min' => 'Your first name must contain at least 2 characters.',
                'last_name.required' => 'Enter your last name.',
                'last_name.min' => 'Your last name must contain at least 2 characters.',
                'phone.required' => 'Enter your phone number.',
                'phone.regex' => 'Enter a valid phone number.',
                'email.required' => 'Enter your email address.',
                'email.email' => 'Enter a valid email address.',
                'address.required' => 'Enter the city or service address.',
                'address.min' => 'Enter the city or service address.',
                'service_slug.required' => 'Select a service.',
                'service_slug.exists' => 'The selected service is unavailable.',
                'message.required' => 'Describe what service you need.',
                'message.min' => 'Add at least 10 characters to describe your request.',
                'source.in' => 'The consultation source is invalid.',
                'privacy.accepted' => 'Consent to data processing is required.',
                'details.array' => 'The additional field format is invalid.',
                'details.max' => 'Too many additional fields were submitted.',
                'website.max' => 'The request was rejected.',
            ],
            'ru' => [
                'first_name.required' => 'Укажите имя.',
                'first_name.min' => 'Имя должно содержать не менее 2 символов.',
                'last_name.required' => 'Укажите фамилию.',
                'last_name.min' => 'Фамилия должна содержать не менее 2 символов.',
                'phone.required' => 'Укажите номер телефона.',
                'phone.regex' => 'Введите корректный номер телефона.',
                'email.required' => 'Укажите электронную почту.',
                'email.email' => 'Введите корректный адрес электронной почты.',
                'address.required' => 'Укажите город или адрес оказания услуги.',
                'address.min' => 'Укажите город или адрес оказания услуги.',
                'service_slug.required' => 'Выберите услугу.',
                'service_slug.exists' => 'Выбранная услуга недоступна.',
                'message.required' => 'Опишите, какая услуга вам нужна.',
                'message.min' => 'Опишите задачу не менее чем в 10 символах.',
                'source.in' => 'Источник консультации указан неверно.',
                'privacy.accepted' => 'Необходимо согласие на обработку данных.',
                'details.array' => 'Неверный формат дополнительных полей.',
                'details.max' => 'Отправлено слишком много дополнительных полей.',
                'website.max' => 'Запрос отклонен.',
            ],
            default => [
                'first_name.required' => 'მიუთითეთ სახელი.',
                'first_name.min' => 'სახელი უნდა შეიცავდეს მინიმუმ 2 სიმბოლოს.',
                'last_name.required' => 'მიუთითეთ გვარი.',
                'last_name.min' => 'გვარი უნდა შეიცავდეს მინიმუმ 2 სიმბოლოს.',
                'phone.required' => 'მიუთითეთ ტელეფონის ნომერი.',
                'phone.regex' => 'ტელეფონის ნომრის ფორმატი არასწორია.',
                'email.required' => 'მიუთითეთ ელფოსტა.',
                'email.email' => 'ელფოსტის ფორმატი არასწორია.',
                'address.required' => 'მიუთითეთ ქალაქი ან მომსახურების მისამართი.',
                'address.min' => 'მიუთითეთ ქალაქი ან მომსახურების მისამართი.',
                'service_slug.required' => 'აირჩიეთ სერვისი.',
                'service_slug.exists' => 'არჩეული სერვისი მიუწვდომელია.',
                'message.required' => 'აღწერეთ რა მომსახურება გჭირდებათ.',
                'message.min' => 'მოთხოვნის აღწერა უნდა შეიცავდეს მინიმუმ 10 სიმბოლოს.',
                'source.in' => 'კონსულტაციის წყარო არასწორია.',
                'privacy.accepted' => 'მონაცემების დამუშავებაზე თანხმობა აუცილებელია.',
                'details.array' => 'დამატებითი ველების ფორმატი არასწორია.',
                'details.max' => 'დამატებითი ველების დასაშვები რაოდენობა გადაჭარბებულია.',
                'website.max' => 'მოთხოვნა უარყოფილია.',
            ],
        };
    }
}
