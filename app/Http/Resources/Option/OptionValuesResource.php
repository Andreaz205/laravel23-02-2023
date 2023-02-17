<?php

namespace App\Http\Resources\Option;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionValuesResource extends JsonResource
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
            'image_url' => $this->image_url,
            'is_default' => $this->is_default,
            'option_name_id' => $this->option_name_id,
            'product_id' => $this->product_id,
            'title' => $this->title,
        ];
    }
}
