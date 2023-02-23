<?php

namespace App\Http\Resources\Api\Hit;

use App\Http\Resources\Api\Color\ColorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HitsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => $this->images()->orderBy('position', 'asc')->first()?->image_url,
            'title' => $this->title,
            'price' => $this->minPrice,
            'colors' => ColorResource::collection($this->colors)
        ];
    }
}
