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
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate avatar only if user has enough points
            if ($this->has('avatar') && $this->avatar) {
                $userPoints = $this->user()->lifetime_points ?? 0;
                if ($userPoints < 50) {
                    $validator->errors()->add(
                        'avatar',
                        'You need at least 50 points to upload a profile picture.'
                    );
                }
            }
        });
    }
}
