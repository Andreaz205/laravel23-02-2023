<?php

namespace App\Http\Services\Product;

use App\Http\Contracts\ProductServiceInterface;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductService implements ProductServiceInterface
{

    public function aggregateOptionsForSingleProduct($product)
    {

    }




    public function categoriesWithCheckedProp($product)
    {
        $categories = Category::all();
        if (count($categories) > 0) {
            $productCategories = $product->categories()->whereNull('category_products.deleted_at')->get();
            if (count($productCategories) > 0) {
                foreach ($categories as $category) {
                    foreach ($productCategories as $productCategory) {
                        if ($productCategory->id == $category->id) {
                            $category->is_checked = true;
                            continue 2;
                        }
                        $category->is_checked = false;
                    }
                }
            }
        }
        return $categories;
    }

    public function getReviewsData(?Product $product)
    {
        $reviews = $product->reviews;
        if (count($reviews)) {
            $marks = $reviews->map(fn ($item) => $item->mark)->toArray();
            $average = array_sum($marks) / count($reviews);
           return ['count' => count($reviews), 'mark' => $average];
        }
        return null;
    }

//    public function appendVariantsCount($products)
//    {
//        return $products;
//    }
}
