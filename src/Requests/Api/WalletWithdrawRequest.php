<?php

namespace admin\wallets\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class WalletWithdrawRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:bank,stripe'],
            // method_details is required when withdrawing to a bank account
            'method_details.account_number'      => ['required_if:method,bank', 'regex:/^[0-9]{9,18}$/'],
            'method_details.ifsc'                => ['required_if:method,bank', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'method_details.account_holder_name' => ['required_if:method,bank', 'string'],
            'method_details.bank_name'           => ['required_if:method,bank', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'method_details.account_number.regex' => 'Account number must contain only digits (9 to 18 digits).',
            'method_details.ifsc.regex' => 'IFSC code must be a valid 11-character code (e.g. ABCD0123456).',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();

        throw new HttpResponseException(response()->json([
            'status' => false,
            'code'   => 422,
            'message'=> $error,
            'data'   => (object) [],
            'errors' => $validator->errors(),
        ], 422));
    }
}
