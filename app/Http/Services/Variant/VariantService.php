<?php

namespace App\Http\Services\Variant;

use App\Http\Contracts\VariantServiceInterface;
use App\Http\Services\Material\MaterialService;
use App\Models\MaterialUnitValueVariants;
use App\Models\OptionValueVariants;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Response;

class  VariantService implements VariantServiceInterface
{
    public function getTitle(&$variant)
    {
        $variant->variant_title = $variant->material_unit_values->map(fn ($item) => $item->value)->join(' ');
    }

    public function getTitleWithProductName(&$variant, $product)
    {
        $variant->variant_title = collect([$product->title, ...$variant->material_unit_values->map(fn ($item) => $item->value)])->join(' ');
    }

    public function appendColorsForVariantFromAggregatedProducts(&$variant, $products)
    {

    }

    public function productColorsForVariant(&$variant, $product)
    {
        $variantsWithColors = $product->variants()
            ->whereHas('material_unit_values', fn($query) => $query->has('color'))
            ->with(['material_unit_values' => fn ($query) => $query->has('color')->with('color')])
            ->get();


        $count = count($variantsWithColors);
        $colors['count'] = $count;
        foreach ($variantsWithColors as $key => $variantWithColor) {
            if ($key >= 5) break;
            $colors['items'][$key] = [...$variantWithColor->material_unit_values->first()->color->toArray(), 'variant_id' => $variantWithColor->id];
        }
        $variant->colors = $colors;
    }

    public function aggregateVariantByMaterialUnits(&$variant)
    {
        $product = Product::with(['variants' => fn($query) => $query->with('material_unit_values'), 'materials' => fn($query) => $query->with('main_material_unit', 'material_units')])->findOrFail($variant->product_id);
        $notes = MaterialUnitValueVariants::query()->whereRelation('variant', 'product_id', '=', $product->id)->get();
        $variant->product = $product;

//        $productValues = $product->option_values;
        $productValues = collect([]);
        $productVars = $product->variants;

        foreach ($productVars as $productVar) {
            $varVals = $productVar->material_unit_values;
            foreach ($varVals as $val) {
                if ($productValues->search(fn($value) => (int)$value->id === (int)$val->id) === false) {
                    $productValues->push($val);
                }
            }
        }

        $materialUnits = [];
        foreach ($product->materials as $material) {
            $units = MaterialService::plainMaterialUnits($material->main_material_unit);
            foreach ($units as $unit) {
                $materialUnits[] = $unit;
            }
        }

        if (isset($materialUnits)) {
            $matValues = [];
            if (isset($productValues)) {
                foreach ($materialUnits as $unit) {
                    foreach ($productValues as $value) {
                        if ((int)$unit->id === (int)$value->material_unit_id) {
                            $matValues[] = $value;
                        }
                    }
                    $unit->values = $matValues;///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $matValues = [];
                }


                $variant->material_units = $materialUnits;
                //Это сложная херня находит опции значения которые есть у варианта с учетом дефолтных значений
                $existsVariantValues = $variant->material_unit_values;


                $variantValues = [];
                foreach ($materialUnits as $materialUnit) {
                    $flag = false;
                    $searchedValue = null;
                    foreach ($materialUnit->values as $nameValue) {
                        foreach ($existsVariantValues as $existsVariantValue) {
                            if ((int)$existsVariantValue->id === (int)$nameValue->id) {
                                $flag = true;
                                $searchedValue = $existsVariantValue;
                                break 2;
                            }
                        }
                    }
                    if ($flag) {
                        $variantValues[] = $searchedValue;
                        $materialUnit->activeValueId = (int)$searchedValue->id;
                    }
                }
//                $variant->variantValues = $variantValues;
//
//                $variantValues = $variant->material_unit_values;
//                $variant->variantValues = $variantValues;
                /////////////////////////////////////////////////////////////////////////////////////////////////////////


                //Обозначаю активные элементы
                foreach ($productValues as $productValue) {
                    foreach ($variantValues as $variantValue) {
                        if ((int)$productValue->id === (int)$variantValue->id) {
                            $productValue->active = true;
                        }
                    }
                }
                $activeIds = [];

                //фильтрация
                foreach ($materialUnits as $key => $materialUnit) {
//                    $curVarUnitKey = array_search(fn ($item) => $item->id === $materialUnit->id, $variant->material_units);
//                    $vals = $variant->material_units[$curVarUnitKey]->values;
//                    $activeId = null;
//                    foreach ($vals as $val) {
////                        if ()
//                    }

                    if ($key == 0) {
                        array_push($activeIds, $materialUnit->activeValueId);
                        continue;
                    }

                    $searchedVariantIds = $this->intersectVariants($activeIds, $notes);
                    $materialUnit->searchedVariantIds = $searchedVariantIds;

                    //Определяем все варианты на текущей итерации у которых до текущей итерации опции совпадают
                    $vals = [];
                    foreach ($searchedVariantIds as $searchedVariantId) {

                        $variantValuesNotes = $notes->filter(fn($item) => (int)$item->variant_id === (int)$searchedVariantId);
                        foreach ($variantValuesNotes as $variantValuesNote) {
                            array_push($vals, (int)$variantValuesNote->material_unit_value_id);
                        }
                        $result = [];
                        foreach ($vals as $key => $val) {
                            if (!in_array($val, $result)) {
                                $result[$key] = $val;
                            }
                        }
                    }

                    $availableValues = [];
                    foreach ($vals as $val) {
                        foreach ($materialUnit->values as $mtVal) {
                            if ((int)$mtVal->id === (int)$val) {
                                if (!in_array($mtVal, $availableValues)) {
                                    array_push($availableValues, $mtVal);
                                }
                            }
                        }
                    }
                    $materialUnit->values = $availableValues;

                    //Попробую здесь построить ссылку важный момент здесь selected ids это предыдущие только
                    $selectedIds = [];
                    foreach ($variantValues as $variantValue) {
                        if ((int)$materialUnit->id === (int)$variantValue->material_unit_id) {
                            break;
                        } else {
                            $selectedIds[] = (int)$variantValue->id;
                        }
                    }

                    $materialUnit->selectedIds = $selectedIds;

                    foreach ($materialUnit->values as $value) {
                        $searchedIds = [...$selectedIds, (int)$value->id];
                        //Над этим я пыхтел 2 недели!!! Относиться осторожно!!!
                        $bingoVariantArray = $this->intersectVariants($searchedIds, $notes);
                        $bingoVariant = null;
                        foreach ($bingoVariantArray as $key => $var) {
                            $bingoVariant = $var;
                            break;
                        }
                        if (isset($bingoVariant)) {
                            $value->linkedVariantId = $bingoVariant;
                        }
                        $value->searchedIds = $searchedIds;
                    }
                    $activeIds[] = (int)$materialUnit->activeValueId;
                }
                // Построение ссылки на вариант для первой опции
                $firstOptionName = $variant->material_units[0];
                foreach ($firstOptionName->values as $nameValue) {
                    $searchedVariantNote = $notes->filter(fn($item) => (int)$item->material_unit_value_id === (int)$nameValue['id'])->first();
                    $searchedVariant = $productVars->filter(fn($item) => (int)$item->id === (int)$searchedVariantNote->variant_id)->first();
                    $nameValue->linkedVariantId = (int)$searchedVariant->id;
                }
            }
        }
        return $variant;
    }

    public function intersectVariants(array $valuesIds, $pivot_notes)
    {
        $resultIds = [];
        foreach ($valuesIds as $key => $value) {
            $resultIds[] = (int)$value;
        }
        $candidates = [];

        foreach ($resultIds as $materialValue) {
            $$materialValue = [];
            foreach ($pivot_notes as $note) {
                if ((int)$note->material_unit_value_id === (int)$materialValue) {
                    array_push($$materialValue, (int)$note->variant_id);
                }
            }
            array_push($candidates, $$materialValue);
        }
        return array_intersect(...$candidates);
    }
}
