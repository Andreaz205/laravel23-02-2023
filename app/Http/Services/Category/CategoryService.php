<?php

namespace App\Http\Services\Category;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryService
{
    public function allCategoryProductsIds(\Illuminate\Database\Eloquent\Model $category): Collection
    {
        $products = collect();
        $childCategories = $category->child_categories()->with('products')->get();
        $categories = [$category, ...$childCategories];
        foreach ($categories as $cat) {
            $currentCategoryProducts = $cat->products;
            foreach ($currentCategoryProducts as $prod) {
                if (!$products->contains($prod)) $products->push($prod);
            }
        }
        return $products->map(fn ($item) => $item->id);
    }

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
