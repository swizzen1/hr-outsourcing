<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => [
                'nullable',
                'date',
                Rule::when($this->input('status') === 'published', ['before_or_equal:now']),
            ],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
