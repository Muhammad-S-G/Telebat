<?php

namespace App\Http\Requests\Payment;

use App\Models\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentIntentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'payment_request_id' => [
                'required',
                'integer',
                Rule::exists('payment_requests', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', PaymentRequest::status()->pending)
                    ->where('user_id', auth('sanctum')->id()),
            ]
        ];
    }
}
