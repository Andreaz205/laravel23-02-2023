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
            'length' => $this->length,
            'height' => $this->height,
            'width' => $this->width,
            'description' => $this->description,
            'parameters' => $this->parameters,
            'images' =>  ImageResource::collection($this->images),
            'option_names' => OptionNamesResource::collection($this->option_names),
            'variants' => VariantResource::collection($this->variants),
        ];
    }
}
