<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:50',
            'mobile_number' => 'nullable|digits:10|min:7|max:15',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name must be less than 50 characters.',
            'mobile_number.digits' => 'The mobile number must be 10 digits.',
            'mobile_number.min' => 'The mobile number must be at least 7 digits.',
            'mobile_number.max' => 'The mobile number must be less than 15 digits.',
            'profile_image.image' => 'The profile image must be an image.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpg, jpeg, png, gif.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.regex' => 'The email format is invalid.',
        ];
    }
} 