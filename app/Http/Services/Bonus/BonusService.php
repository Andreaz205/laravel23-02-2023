<?php

namespace App\Http\Services\Bonus;

use App\Models\Category;

class BonusService
{
    public function categoriesWithCheckedProp($bonus)
    {
        $categories = Category::all();
        $bonusCategories = $bonus->categories;
        if (count($bonusCategories) === 0 || count($categories) === 0) return $categories;
        foreach ($categories as $category) {
            foreach ($bonusCategories as $bonusCategory) {
                if ($category->id == $bonusCategory->id) {
                    $category->is_checked = true;
                    continue 2;
                }
            }
        }
        return $categories;
    }
}
