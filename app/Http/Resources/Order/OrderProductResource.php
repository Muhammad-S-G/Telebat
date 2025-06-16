<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'store_id' => $this->store_id,
            'name' => $this->getName($locale),
            'description' => $this->getDescription($locale),
            'price' => $this->price,
            'quantity' => $this->pivot->quantity,
            'product_total' => $this->pivot->price * $this->pivot->quantity,
            'image' => $this->image,
            'is_favorite' => (bool)$this->is_favorite
        ];
    }
}
