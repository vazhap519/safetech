<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewInvitationSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'author' => $this->input('author', $this->input('name')),
        ]);
    }

    public function rules(): array
    {
        return [
            'author' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'locale' => ['nullable', 'string', 'in:ka,en,ru'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return match ($this->input('locale')) {
            'en' => [
                'author.required' => 'Enter your name.',
                'author.max' => 'Your name is too long.',
                'company.max' => 'The company or property name is too long.',
                'role.max' => 'The role is too long.',
                'quote.required' => 'Write your review.',
                'quote.max' => 'The review is too long.',
                'consent.accepted' => 'Consent is required before submitting the review.',
                'website.max' => 'The review could not be submitted.',
            ],
            'ru' => [
                'author.required' => 'Укажите ваше имя.',
                'author.max' => 'Имя слишком длинное.',
                'company.max' => 'Название компании или объекта слишком длинное.',
                'role.max' => 'Название должности слишком длинное.',
                'quote.required' => 'Напишите ваш отзыв.',
                'quote.max' => 'Отзыв слишком длинный.',
                'consent.accepted' => 'Перед отправкой отзыва необходимо дать согласие.',
                'website.max' => 'Не удалось отправить отзыв.',
            ],
            default => [
                'author.required' => 'მიუთითეთ თქვენი სახელი.',
                'author.max' => 'სახელი ზედმეტად გრძელია.',
                'company.max' => 'კომპანიის ან ობიექტის დასახელება ზედმეტად გრძელია.',
                'role.max' => 'თანამდებობის დასახელება ზედმეტად გრძელია.',
                'quote.required' => 'დაწერეთ თქვენი შეფასება.',
                'quote.max' => 'შეფასების ტექსტი ზედმეტად გრძელია.',
                'consent.accepted' => 'შეფასების გაგზავნამდე თანხმობა აუცილებელია.',
                'website.max' => 'შეფასების გაგზავნა ვერ მოხერხდა.',
            ],
        };
    }
}
