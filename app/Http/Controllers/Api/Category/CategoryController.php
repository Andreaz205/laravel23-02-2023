<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Services\Variant\VariantService;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    /**
     * @throws Exception
     */

    public function categoryVariants(Category $category, VariantService $variantService)
    {
        if (! isset($category->parent_category_id)) {
            $mainCategoryProducts = $category->products()->whereHas('variants')->with(['variants' => fn ($query) => $query->with(['images', 'material_unit_values' => fn ($query) => with('color')])->limit(6)])->limit(10)->get();
            $category->products = $mainCategoryProducts;
            $childCategories = $category->child_categories()
                ->with(['products' => fn ($query) => $query->whereHas('variants')->with(['variants' => fn ($query) => $query->with(['images', 'material_unit_values'])->limit(6)])->limit(10)])
                ->get();

            foreach ($childCategories as $childCategory) {
                $variants = collect([]);
                foreach ($childCategory->products as $product) {
                    foreach ($product->variants as $variant) {
                        $variantService->getTitleWithProductName($variant, $product);
                        unset($variant->material_unit_values);
                        $variants->push($variant);
                    }
                    unset($childCategory->products);
                }
                $childCategory->variants = $variants;
            }

            $mainCategoryVariants = collect([]);
            foreach ($category->products as $product) {
                foreach ($product->variants as $variant) {
                    $variantService->getTitleWithProductName($variant, $product);
                    unset($variant->material_unit_values);
                    $mainCategoryVariants->push($variant);
                }
                unset($product->variants);
            }
            unset($category->products);

//            $variantService->productColorsForVariant();
            // В конце варианты которые не привязаны к дочерним но привязаны к основной категории закидываю в others
            return Response::json([
                'main_category' => $category,
                'child_categories' => $childCategories,
                'others' => $mainCategoryVariants
            ]);

        } else {
            $products = $category->products()
                ->with(['variants' => fn ($query) => $query->with([
                    'material_unit_values' => fn ($query) => $query->with('color'),
                    'images' => fn ($query) => $query->limit(6)
                ])
                ->limit(10)])
                ->limit(20)
                ->get();

            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $variantService->getTitle($variant);
                    unset($variant->material_unit_values);
                }
            }

            return $products;
        }
    }


}
