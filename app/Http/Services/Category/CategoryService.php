<?php

namespace App\Http\Services\Category;

use App\Models\Category;

class CategoryService
{
    public function nestedCategories($categories)
    {
        $previousCategories = [];
        foreach ($categories as $category) {
            if ($category->parent_category_id === null) {
                $this->appendChildrenToCategory($category, $categories);
                $previousCategories[] = $category;
            }
        }
        return $previousCategories;
    }

    public function appendChildrenToCategory(&$category, $listCategories)
    {
        $childCategories = [];
        foreach ($listCategories as $listCategoryItem) {
            if ($listCategoryItem->parent_category_id == $category->id) {
                $childCategories[] = $listCategoryItem;
            }
        }
        $category->child_categories = $childCategories;
        foreach ($category->child_categories as $childCategory) {
            $this->appendChildrenToCategory($childCategory, $listCategories);
        }
    }
}
