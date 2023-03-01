<?php

namespace App\Http\Services\Group;

use App\Models\Category;
use App\Models\Group;

class GroupService
{
    public function categoriesWithCheckedProp(Group $group)
    {
        $categories = Category::all();
        if (count($categories) > 0) {
            $groupCategories = $group->discounted_categories;
            if (count($groupCategories) > 0) {
                foreach ($categories as $category) {
                    foreach ($groupCategories as $groupCategory) {
                        if ($groupCategory->id == $category->id) {
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
}
