<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('locale', 'en');
        return [
            'store_id' => $this->store_id,
            'name' => $this->getName($locale),
            'description' => $this->getDescription($locale),
            'price' => $this->price,
            'quantity' => $this->pivot->quantity,
            'image' => $this->image,
        ];
    }
}
