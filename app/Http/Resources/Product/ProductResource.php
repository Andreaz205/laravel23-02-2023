<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Image\ImageResource;
use App\Http\Resources\Option\OptionNamesResource;
use App\Http\Resources\Variant\VariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'allow_sales' => $this->allow_sales,
            'title' => $this->title,
            'height' => $this->height,
            'wight' => $this->weight,
            'images' =>  ImageResource::collection($this->images),
            'option_names' => OptionNamesResource::collection($this->option_names),
            'variants' => VariantResource::collection($this->variants),
        ];
    }
}
