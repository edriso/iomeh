<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'min:3', 'regex:/^[a-zA-Z0-9_]+$/'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'week_starts_on' => ['nullable', 'integer', 'in:0,1,6'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'username.min' => __('validation.username.min'),
            'username.regex' => __('validation.username.regex'),
            'name.min' => __('validation.name.min'),
            'name.max' => __('validation.name.max'),
            'bio.max' => __('validation.bio.max'),
            'website_url.url' => __('validation.website_url.url'),
            'week_starts_on.in' => __('validation.in'),
        ];
    }
}
