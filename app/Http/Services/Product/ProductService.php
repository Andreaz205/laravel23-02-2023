<?php

namespace App\Http\Services\Product;

use App\Http\Contracts\ProductServiceInterface;
use App\Models\Category;

class ProductService implements ProductServiceInterface
{

    public function aggregateOptionsForCollection($products)
    {
        if (isset ($products)) {
            foreach ($products as $product) {
                $productOptions = $product->options;
                if (isset ($productOptions)) {
                    $columnNames = $this->calculateColumnNames($productOptions);
                    $product->columns = $columnNames;
                }
            }
            return $products;
        }
        return null;
    }

    public function calculateColumnNames($productOptions)
    {
        $columnNames = [];
        foreach($productOptions as $productOption) {
            if (!in_array($productOption->title, $columnNames)) {
                $columnNames[] = $productOption->title;
            }
        }
        return $columnNames;
    }

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
        if (isset($allCategories) && count($allCategories) > 0) {
            $productCategories = $product->categories()->whereNull('category_products.deleted_at')->get();
            if (isset($productCategories) && count($productCategories) > 0) {
                foreach ($allCategories as $category) {
                    foreach($productCategories as $productCategory) {
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
        if (isset($categories) && count($categories) > 0) {
            $categories->load('child_categories');
        }
        return $categories;
    }

    public function addIsDefaultFlagToValues($optionValues)
    {
        if (isset($optionValues) && count($optionValues) > 0) {
            foreach($optionValues as $optionValue) {

            }
        }
    }
}
