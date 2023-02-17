<?php

namespace App\Http\Resources\Variant;

use Illuminate\Http\Resources\Json\JsonResource;

class CreatedVariantResource extends JsonResource
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
            'option_values' => $this->option_values
        ];
    }
}
