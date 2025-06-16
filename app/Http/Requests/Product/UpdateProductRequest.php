<?php

namespace App\Http\Requests\Product;

use App\Models\Store;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        $storeVendor = $product->store->vendor_id;
        return auth('sanctum')->user()->id === $storeVendor;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ar_name' => 'sometimes|string|max:255',
            'en_name' => 'sometimes|string|max:255',
            'ar_description' => 'sometimes|string',
            'en_description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'quantity' => 'sometimes|numeric',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $response = validationError($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
