<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Services\Variant\VariantService;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function catalog(VariantService $variantService)
    {
        $parentCategories = Category::query()
            ->whereNull('parent_category_id')
            ->with(['child_categories' => function ($query) {
                return $query->with(['variants' => function ($query) {
                    return $query->with(
                        [
                            'product',
                            'images' => fn ($query) => $query->limit(6),
                            'material_unit_values' => fn ($query) => $query->with('color')
                        ]
                    )->limit(10);
                }]);
            }])
            ->with(['variants' => function ($query) {
                return $query->with(
                    [
                        'product',
                        'images' => fn ($query) => $query->limit(6),
                        'material_unit_values' => fn ($query) => $query->with('color')
                    ]
                )->limit(10);
            }])
            ->get();
        $response = collect([]);
        foreach ($parentCategories as $parentCategory) {
            $childCategories = $parentCategory->child_categories;
            $childVariants = collect([]);

            foreach ($parentCategory->variants as $variant) {
                $product = $variant->product;
                $variantService->getTitleWithProductName($variant, $product);
                unset($variant->material_unit_values, $variant->product);
                $childVariants->push($variant);
            }

            foreach ($childCategories as $childCategory) {
                $variants = $childCategory->variants;
                foreach ($variants as $variant) {
                    $product = $variant->product;
                    $variantService->getTitleWithProductName($variant, $product);
                    unset($variant->material_unit_values, $variant->product);
                    $childVariants->push($variant);
                }
            }
            $response[] = [
                'category' => $parentCategory->name,
                'variants' => $childVariants,
            ];
        }
        return $response;

    }

    /**
     * @throws Exception
     */
    public function parentCategoryVariants(Category $category, VariantService $variantService)
    {
        if (! isset($category->parent_category_id)) {
            $mainCategoryProducts = $category
                ->products()
                ->whereHas('variants')
                ->with(['variants' => fn ($query) => $query->with(['images' => fn ($query) => $query->limit(6), 'material_unit_values' => fn ($query) => $query->with('color')])->limit(20)])
                ->limit(10)
                ->get();
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
        }
        throw ValidationException::withMessages(['message' => 'Некорректная категория для маршрута!']);
    }

    /**
     * @throws ValidationException
     */
    public function childCategoryVariants(Category $category, VariantService $variantService)
    {
        if (isset($category->parent_category_id)) {
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
        throw ValidationException::withMessages(['message' => 'Некорректная категория для маршрута!']);
    }

}
