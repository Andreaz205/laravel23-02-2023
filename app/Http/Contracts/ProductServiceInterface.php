<?php

namespace App\Http\Contracts;

use App\Models\Category;

interface ProductServiceInterface
{
//    public function aggregateOptionsForCollection($products);
//    public function calculateColumnNames($productOptions);
    public function aggregateOptionsForSingleProduct($product);
    public function categoriesWithCheckedProp($product);
    public function addChildCategories(Category $allCategories);
    public function mainCategoryChildren(&$mainCategory, $categories);
//    public function appendVariantsCount($products);
}
