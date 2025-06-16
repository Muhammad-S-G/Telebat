<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveFromOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = Order::where('id', $this->route('order')->id)
            ->where('user_id', $this->user()->id)
            ->first();

        if (!$order) {
            throw new AuthorizationException('Order not found or cannot temper with', 403);
        }

        if ($order->status !== 'pending') {
            throw new AuthorizationException("Cannot remove product your order status is: {$order->status}");
        }
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
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::exists('order_product', 'product_id')
                    ->where('order_id', $this->route('order')->id)
            ]
        ];
    }

    public function messages()
    {
        return [
            'product_id.exists' => 'The product does not exist in the specified order.'
        ];
    }
}
