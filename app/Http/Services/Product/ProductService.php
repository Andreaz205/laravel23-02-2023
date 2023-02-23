<?php

namespace App\Http\Services\Product;

use App\Http\Contracts\ProductServiceInterface;
use App\Models\Category;

class ProductService implements ProductServiceInterface
{

//    public function aggregateOptionsForCollection($products)
//    {
//        if (isset ($products)) {
//            foreach ($products as $product) {
//                $productOptions = $product->options;
//                if (isset ($productOptions)) {
//                    $columnNames = $this->calculateColumnNames($productOptions);
//                    $product->columns = $columnNames;
//                }
//            }
//            return $products;
//        }
//        return null;
//    }

//    public function calculateColumnNames($productOptions)
//    {
//        $columnNames = [];
//        foreach($productOptions as $productOption) {
//            if (!in_array($productOption->title, $columnNames)) {
//                $columnNames[] = $productOption->title;
//            }
//        }
//        return $columnNames;
//    }

    public function aggregateOptionsForSingleProduct($product)
    {
        if (isset($product) ) {
            $optionNames = $product->option_names;
            foreach ($optionNames as $optionName) {
                $optionName->option_values;
            }
            $product->option_names = $optionNames;
        }
        return $product;
    }

    public function categoriesWithCheckedProp($product)
    {
        $allCategories = Category::all();
        if (count($allCategories) > 0) {
            $productCategories = $product->categories()->whereNull('category_products.deleted_at')->get();
            if (count($productCategories) > 0) {
                foreach ($allCategories as $category) {
                    foreach ($productCategories as $productCategory) {
                        if ($productCategory->id === $category->id) {
                            $category->is_checked = true;
                            continue 2;
                        }
                        $category->is_checked = false;
                    }
                }
            }
        }
        $allCategories = $this->addChildCategories($allCategories);
        return $allCategories;
    }

    public function addChildCategories($categories)
    {
        $mainCategories = [];
        if (isset($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                if ($category->parent_category_id === null) {
                    $mainCategories[] = $category;
                }
            }
        }
        if (count($mainCategories) === 0) return null;
        foreach ($mainCategories as $mainCategory) {
            $this->mainCategoryChildren($mainCategory, $categories);

        }
        return $mainCategories;
    }

    public function mainCategoryChildren(&$mainCategory, $categories)
    {
        $nextMainCategories = [];
        foreach ($categories as $category) {
            if ($category->parent_category_id === $mainCategory->id) {
                $nextMainCategories[] = $category;
            }
        }
        if (count($nextMainCategories) === 0) return $mainCategory->child_categories = null;
        foreach ($nextMainCategories as $nextMainCategory) {
            $this->mainCategoryChildren($nextMainCategory, $categories);
        }
        $mainCategory->child_categories = $nextMainCategories;
    }

//    public function appendVariantsCount($products)
//    {
//        return $products;
//    }
}
