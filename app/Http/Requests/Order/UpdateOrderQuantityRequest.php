<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request or do it in policy
     */
    public function authorize()
    {
        $order = Order::where('id', $this->route('order')->id)
            ->where('user_id', $this->user()->id)
            ->first();

        if (!$order) {
            throw new AuthorizationException('Order not found or cannot be updated', 403);
        }
        if ($order->status !== 'pending') {
            throw new AuthorizationException("Order cannot be update , order status is: {$order->status}", 403);
        }
        return true;
    }

    protected function failedAuthorization() {}

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
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1'
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
