<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => str_replace('+', '', $this->phone)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'nullable',
                'string',
                'max:255',
                'min:2',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u'
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255',
                'min:2',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u'
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id())
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/',
                Rule::unique('users', 'phone')->ignore(Auth::id())
            ],
        ];
    }
}
