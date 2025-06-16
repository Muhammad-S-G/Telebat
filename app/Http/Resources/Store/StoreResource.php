<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'name' => $this->getName($locale),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $this->image,
        ];
    }
}
