<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;

class CategoryController extends Controller
{
    /**
     * @throws Exception
     */
    public function mainCategoryProducts(Category $category)
    {
        if (isset($category->parent_category_id)) throw new Exception('Некорректная категория!', 404);
        $childCategories = $category->child_categories()->with(['products'])->get();
        return $childCategories;
    }

    /**
     * @throws Exception
     */
    public function childCategoryProducts(Category $category)
    {
        if (! isset($category->parent_category_id)) throw new Exception('Некорректная категория!', 404);
        $products = $category->products;
        return $products;
    }
}
