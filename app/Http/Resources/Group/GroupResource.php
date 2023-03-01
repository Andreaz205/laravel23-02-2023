<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'title' => $this->title,
            'discount' => $this->discount,
            'is_default' => $this->is_default,
            'allow_discounted' => $this->allow_discounted,
            'allow_kits' => $this->allow_kits,
            'discount_description' => $this->discount_description,
            'categories' => $this->discounted_categories,
        ];
    }
}
