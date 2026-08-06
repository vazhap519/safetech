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
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }
}
