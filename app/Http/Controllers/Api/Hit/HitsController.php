<?php

namespace App\Http\Controllers\Api\Hit;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Hit\HitsResource;
use App\Models\Category;
use App\Models\CategoryProducts;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HitsController extends Controller
{
    public function data()
    {
        $hitVariants = Variant::where('ordered_count', '>', 0)
            ->with(['images' => function ($query) {
            $query->limit(1);
        }, 'material_unit_values'])
            ->limit(10)
            ->get();

        $itemsCount = count($hitVariants);
        if ($itemsCount < 10) {
            $needMoreItems = 10 - $itemsCount;
            $moreVariants = Variant::with(['images' => function ($query) {
                $query->limit(1);
            }, 'material_unit_values'])
                ->limit($needMoreItems)->get();
            $hitVariants->push(...$moreVariants);
        }

        foreach ($hitVariants as $variant) {
            $variant->title = $variant->getTitleAttribute();

        }

        return $hitVariants;
    }
}
