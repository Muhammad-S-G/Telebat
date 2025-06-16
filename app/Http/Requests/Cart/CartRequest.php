<?php

namespace App\Http\Requests\Cart;

use App\Rules\QuantityAvailable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => ['required', 'integer', 'min:1', new QuantityAvailable($this->input('product_id'))]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = validationError($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
