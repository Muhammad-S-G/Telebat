<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class GetInvoiceRequest extends FormRequest
{
    private Payment $payment;

    public function prepareForValidation()
    {
        $payment = $this->route('payment');
        if (is_int($payment)) {
            $this->payment = Payment::where('id', $payment)->firstOrFail();
        } else {
            $this->payment = $payment;
        }

        $this->merge(['payment' => $this->payment->id]);
        $this->merge([
            'download' => filter_var($this->input('download'), FILTER_VALIDATE_BOOLEAN)
        ]);
    }

    public function authorize(): bool
    {
        return ($user_id = auth('sanctum')->id()) and $user_id == $this->payment->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($this->payment->status != Payment::status()->completed) {
                        $fail("Payment status is {$this->payment->status}, it must be {$this->payment::status()->completed}");
                    }
                }
            ],
            'download' => ['nullable', 'boolean']
        ];
    }
}
