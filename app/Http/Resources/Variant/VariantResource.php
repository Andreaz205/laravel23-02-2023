<?php

namespace App\Http\Resources\Variant;

use App\Http\Resources\Image\ImageResource;
use App\Http\Resources\Option\OptionValuesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
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
            'code' => $this->code,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'purchase_price' => $this->purchase_price,
            'quantity' => $this->quantity,
            'images' => ImageResource::collection($this->images),
        ];
    }
}
