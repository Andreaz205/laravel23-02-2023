<?php

namespace App\Http\Controllers\Api\Interior;

use App\Http\Controllers\Controller;
use App\Models\Interior;
use App\Models\Variant;
use Illuminate\Http\Request;

class InteriorController extends Controller
{
    public function mainPageData()
    {
        $interiors = Interior::with(['image', 'variants' => function ($query) {
            $query->with(['material_unit_values' => fn ($query) => $query->with('color')]);
        }])->get();

        foreach ($interiors as $interior) {
            $variants = $interior->variants;
            foreach ($variants as $variant) {
                $currentVariant = $variant;
                $availableColors = collect();

                $otherVariants = Variant::whereNot('id', $variant->id)->where('product_id', $variant->product_id)->with(['material_unit_values' => function ($query) {
                    $query->whereHas('color')->with('color')->limit(1);
                }])->withCount('material_unit_values')->limit(5)->get();

                foreach ($otherVariants as $otherVariant) {
                    $currColor = null;
                    $color = $otherVariant->material_unit_values[0]->color;
                    $color->variant_id = $otherVariant->id;
                    foreach ($currentVariant->material_unit_values as $currentVariantValue) {
                        if (isset($currentVariantValue->color)) $currColor = $currentVariantValue->color;
                    }
                    if (!$availableColors->contains($color) && $currColor->id !== $color->id) $availableColors->push($color);
                }
                $variant->title = $variant->getTitleAttribute();
                $variantLinksColors = [];
                foreach ($availableColors as $availableColor) {
                    $variantLinksColors['id'] = $availableColor->id;
                    $variantLinksColors['image_url'] = $availableColor->image_url;
                    $variantLinksColors['variant_id'] = $availableColor->variant_id;
                };
                $variant->colors = $availableColors;
            }
        }
//        $variants = Variant::whereIn('product_id', $productIds)
//            ->with(['material_unit_values' => function ($query) {
//                $query->whereHas('color')->with('color');
//            }])
//            ->get();
//
//        foreach ($variants as $variant) {
//            $variant->color = $variant->material_unit_values[0]->color;
//        }

        return $interiors;
    }


}
