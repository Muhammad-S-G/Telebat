<?php

namespace App\Http\Requests\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $store = $this->route('store');
        return auth('sanctum')->user()->id === $store->vendor_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => 'sometimes|exists:sections,id',
            'ar_name' => 'sometimes|string|max:255',
            'en_name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'image.image' => 'The file must be an image',
            'image.mimes' => 'The image format can only be: jpeg, png, jpg, svg, gif',
            'image.max' => 'The image size must not exceed 2MB'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = validationError($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
