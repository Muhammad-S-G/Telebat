<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'store_id' => $this->store_id,
            'section_id' => $this->section_id,
            'name' => $this->name[$locale] ?? null,
            'description' => $this->description[$locale] ?? null,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'image' => $this->image,
            'is_favorite' => (bool)$this->is_favorite,
        ];
    }
}
