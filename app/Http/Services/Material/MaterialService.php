<?php

namespace App\Http\Services\Material;

use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialUnit;
use App\Models\OptionValue;
use App\Models\Variant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MaterialService
{
    public static function plainMaterialUnits($mainMaterialUnit): array
    {
        $units = [];
        $currentUnit = $mainMaterialUnit;
        if (isset($currentUnit)) $units[] = $currentUnit;
        while (isset($currentUnit['child_unit'])) {
            $currentUnit = $currentUnit['child_unit'];
            $units[] = $currentUnit;
        }
        return $units;
    }

    public static function products(Category $category)
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
        return $products;
    }

    public static function productsVariants($products)
    {
        $productsIds = [];
        foreach ($products as $product) {
            $productsIds[] = $product->id;
        }
        return Variant::whereIn('product_id', $productsIds)->get();

    }

    public static function syncMaterialsToVariant($variants, $materialIds)
    {
        $materials = Material::whereIn('id', $materialIds)->get();
        $materialUnits = collect();
        foreach ($materials as $material) {
            $materialUnits->push(...$material->material_units);
        }
        $result = [];
        $values = collect();
        foreach ($materialUnits as $materialUnit) {
            foreach ($variants as $variant) {
                $result[] = [
                    'variant_id' => $variant->id,
                    'material_unit_id' => $materialUnit->id,
                ];
            }
        }
        OptionValue::insert($result);
    }

    public function getSets($materials)
    {
        $sets = [];
        $materialIds = $materials->map(fn($item) => $item->id);
        $allMaterialUnits = MaterialUnit::whereIn('material_id', $materialIds)->with(['values' => fn ($query) => $query->with('color')])->get();
        foreach ($materials as $material) {
            $materialSet = [];
            $materialSet['material_id'] = $material->id;
            $matSets = [];
//            dd($material->material_units);
//            $materialUnits = $material->material_units;
            $materialUnits = $allMaterialUnits->filter(fn($item) => (int)$item->material_id === (int)$material->id);
            if (count($materialUnits)) {

                if (count($materialUnits) == 1) {
                    $values = $materialUnits->first()->values;
                    $result = $values->map(fn($value) => [
                        0 => [
                            'id' => $value->id, 'value' => $value->value
                        ],
                        'title' => $value->value,
                        'ids' => [$value->id],
                        'color' => $value->color
                    ]);
                    $materialSet['sets'] = $result;
                } else {
                    $materialValues = $this->allMaterialValues($materialUnits);
                    $lastUnit = $materialUnits[count($materialUnits) - 1];
                    $lastUnitValues = $lastUnit->values;
                    foreach ($lastUnitValues as $value) {
                        $set = $this->getSetByLastValue($value, $materialValues);
                        $arrayWithTitleAndIds = $this->getSetTitleWithIds(array_reverse($set));
                        $set['title'] = $arrayWithTitleAndIds['title'];
                        $set['ids'] = $arrayWithTitleAndIds['ids'];
                        $set['color'] = $value->color;
                        $matSets[] = $set;
                    }
                    $materialSet['sets'] = $matSets;
                }
                $sets[] = $materialSet;
            } else {
                $materialSet['sets'] = [];
            }
        }
        return $sets;
    }


    public function getSetByLastValue($materialLastUnitValue, $allMaterialValues)
    {
        $set = [[
                'id' => $materialLastUnitValue->id, 'value' => $materialLastUnitValue->value
        ]];
        $currentValue = $materialLastUnitValue;
        $parentValueKey = $allMaterialValues->search(fn ($value, $key) => (int)$value->id === (int)$currentValue->parent_material_unit_value_id);
        while ($parentValueKey !== false) {
            $currentValue = $allMaterialValues[$parentValueKey];
            $set[] = ['id' => $currentValue->id, 'value' => $currentValue->value];
            $parentValueKey = $allMaterialValues->search(fn ($value, $key) => (int)$value->id === (int)$currentValue->parent_material_unit_value_id);
        };
        return $set;
    }

    /**
     * @throws ValidationException
     */
    public function validateToggleColor(Material $material)
    {
        //Получаю категории у которых указан данный материал и релатионах запрашиваю материалы к ним без него самого
        $categoriesWithThisMaterial = Category::query()
            ->whereRelation('materials', 'material_id', '=', $material->id)
            ->with(['materials' => fn ($query) => $query->whereNot('material_id', $material->id)])
            ->get();
        foreach ($categoriesWithThisMaterial as $category) {
            $categoryMaterials = $category->materials;
            foreach ($categoryMaterials as $categoryMaterial) {
                if ($categoryMaterial->with_colors) {
                    $message =
                        'Ошибка! Невозможно передать данному материалу цвета! Причина: У категории '
                        . $category->name .
                        ' указан этот материал и также другой  - '
                        . $categoryMaterial->title .
                        ', в котором уже переданы цвета! Решение: 1) Убрать цвета у указанного материала (для опытных пользователей) 2) Создавать новые, не привязанные материалы';
                    throw ValidationException::withMessages([$message]);
                }
            }
        }
    }

    public function allMaterialValues($materialUnits)
    {
        $values = [];
        foreach ($materialUnits as $materialUnit) {
            $values = [...$values, ...$materialUnit->values];
        }
        return collect($values);
    }

    public function getSetTitleWithIds($set): array
    {
        $title = '';
        $ids = [];
        foreach ($set as $item) {
            $ids[] = $item['id'];
            $value = $item['value'];
            $title .= $value . " ";
        }
        return ['title' => trim($title), 'ids' => $ids];
    }

    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

//    public function getSetTitleWithIds($set): string
//    {
//        $title = '';
//        foreach ($set as $item) {
//            $value = $item['value'];
//            $title .= $value . " ";
//        }
//        return trim($title);
//    }

//    public function getSetIds($set): array
//    {
//        $ids = [];
//        foreach ()
//    }
}
