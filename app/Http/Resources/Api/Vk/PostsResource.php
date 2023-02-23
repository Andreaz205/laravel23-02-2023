<?php

namespace App\Http\Resources\Api\Vk;

use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
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
            'created_time' => $this->created_time,
            'image_url' => $this->image_url,
            'content' => $this->content,
            'title' => $this->title,
            'views' => $this->views,
        ];
    }
}
