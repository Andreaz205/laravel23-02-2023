<?php

namespace App\Http\Resources\Material;

use App\Http\Resources\MaterialUnitResource;
use App\Http\Services\Material\MaterialService;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'with_colors' => $this->with_colors,
            'material_unit' => isset($this->main_material_unit) ? MaterialUnitResource::make($this->main_material_unit)->resolve() : null,
            'plain_units' => MaterialService::plainMaterialUnits($this->main_material_unit),
        ];
    }
}
