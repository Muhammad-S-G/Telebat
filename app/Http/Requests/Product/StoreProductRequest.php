<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreProductRequest extends FormRequest
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
            'store_id' => 'required|exists:stores,id',
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'ar_description' => 'required|string',
            'en_description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = validationError($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
