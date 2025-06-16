<?php

namespace App\Http\Requests\Section;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateSectionRequest extends FormRequest
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
            'ar_name' => 'sometimes|string|max:255',
            'en_name' => 'sometimes|string|max:255',
            'ar_description' => 'sometimes|string',
            'en_description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image format can only be: jpeg, jpg, png, gif, svg.',
            'image.max' => 'The image size must not exceed 2MB'
        ];
    }

    public function failedValidator(Validator $validator)
    {
        $response = validationError($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
