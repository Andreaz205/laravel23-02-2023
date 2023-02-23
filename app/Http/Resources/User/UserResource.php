<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Group\GroupResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        'email' => $this->email,
        'group_id' => $this->group_id,
        'id' => $this->id,
        'kind' => $this->kind,
        'name' => $this->name,
        'group' => new GroupResource($this->group)
        ];
    }
}
