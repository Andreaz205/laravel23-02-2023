<?php

namespace App\Http\Controllers\Api\Kit;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use Illuminate\Http\Request;

class KitsController extends Controller
{
    public function mainPageData()
    {
        $kits = Kit::with(['products' => fn ($query) => $query->with(
                ['variants' => fn ($query) => $query->limit(1)
                    ->with(['material_unit_values'])
                ]
            )])
            ->get();

        $kits->each(function ($item) {
            $products = $item->products;
            $products->each(function ($product) {
                $variant =  $product->variants[0];
                $title = $variant->title;
                $variant->title = $title;
            });
        });
        return $kits;
    }
}
