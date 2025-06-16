<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuantityAvailable implements ValidationRule
{

    public function __construct(public $productId) {}


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::find($this->productId);
        if (!$product) {
            $fail('The selected product does not exist');
            return; 
        }

        if ($product->quantity < $value) {
            $fail('The requested quantity exceeds the available stock for this product.');
        }
    }
}
