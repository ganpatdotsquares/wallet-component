<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class ValidateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            'name' => 'required|max:20',
            'email' => 'required|email',
            'mobile' => 'numeric|digits:10',
        ];
    }
    
    public function messages(): array
    {
        return [
            'mobile.digits' => 'Your mobile number must be 10 digits.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }
    
}
