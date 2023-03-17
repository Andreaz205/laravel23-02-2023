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
        if ($this->kind === 'single') {
            return [
                'email' => $this->email,
                'group_id' => $this->group_id,
                'phone' => $this->phone,
                'id' => $this->id,
                'kind' => $this->kind,
                'name' => $this->name,
                'is_subscribed_to_news' => $this->is_subscribed_to_news,
                'group' => new GroupResource($this->group),
            ];
        }
        return [
            'email' => $this->email,
            'group_id' => $this->group_id,
            'id' => $this->id,
            'kind' => $this->kind,
            'name' => $this->name,
            'group' => new GroupResource($this->group),
            'is_subscribed_to_news' => $this->is_subscribed_to_news,
            'phone' => $this->phone,
            'jural_address' => $this->jural_address,
            'additional_phone' => $this->additional_phone,
            'ogrn' => $this->ogrn,
            'bic' => $this->bic,
            'inn' => $this->inn,
            'bank_name' => $this->bank_name,
            'correspondent_account' => $this->correspondent_account,
            'calculated_account' => $this->calculated_account,
            'unloading_address' => $this->unloading_address,
        ];
    }
}
