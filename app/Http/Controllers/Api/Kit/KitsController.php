<?php

namespace App\Http\Controllers\Api\Kit;

use App\Http\Controllers\Controller;
use App\Http\Services\Product\ProductService;
use App\Models\Kit;
use App\Models\Variant;
use Illuminate\Http\Request;

class KitsController extends Controller
{
    public function mainPageData(ProductService $productService)
    {
        $kits = Kit::with(['products' => fn ($query) => $query->with(['reviews' =>  fn ($query) => $query->where('published', true)])])->get();

        $kits->each(function ($item) use ($productService) {
            $products = $item->products;
            $products->each(function  ($product) use($productService) {
                $kitVariant = Variant::where('id', $product->pivot->variant_id)->with(['material_unit_values', 'images' => fn ($query) => $query->limit(1)])->first();
                if (isset($kitVariant)) {
                    $kitVariant->title = $kitVariant->getTitleAttribute();
                }
                $product->variant = $kitVariant;

                $reviewsData = $productService->getReviewsData($product);
                $product->reviews_data = $reviewsData;
            });
        });

        return $kits;
    }
}
