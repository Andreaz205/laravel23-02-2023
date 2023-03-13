<?php

namespace App\Http\Resources\AccentProperty;

use Illuminate\Http\Resources\Json\JsonResource;

class AccentPropertyResource extends JsonResource
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
            'description' => $this->description,
            'media' => MediaResource::make($this->media)->resolve(),
        ];
    }
}
