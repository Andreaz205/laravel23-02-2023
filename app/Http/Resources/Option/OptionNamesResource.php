<?php

namespace App\Http\Resources\Option;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionNamesResource extends JsonResource
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
            'default_option_value_id' => $this->default_option_value_id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'option_values' => $this->option_values,
        ];
    }
}
