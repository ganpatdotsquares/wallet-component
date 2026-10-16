<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiGlobalFunctions;


class WalletWithdrawRequest extends FormRequest
{
    use ApiGlobalFunctions;

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
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:bank'],
            'method_details.account_number' => ['required', 'string', 'min:9', 'max:18'],
            'method_details.ifsc' => ['required', 'string', 'size:11'],
            'method_details.account_holder_name' => ['required', 'string'],
            'method_details.bank_name' => ['required', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();
        return $this->sendValidationError($error);
    }
}
