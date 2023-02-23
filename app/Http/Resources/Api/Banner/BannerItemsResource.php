<?php

namespace App\Http\Resources\Api\Banner;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerItemsResource extends JsonResource
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
            'video_url' => $this->video_url,
        ];
    }
}
