<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;

class AccumulativeDiscountResource extends JsonResource
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
            'type' => $this->type,
            'value' => $this->value,
            'allow_discounted' => $this->allow_discounted,
            'allow_kits' => $this->allow_kits,
            'threshold' => $this->threshold,
            'available_groups' => $this->available_groups,
            'categories' => $this->categories,
            'groups' => $this->groups,
            'description' => $this->description,
        ];
    }
}
