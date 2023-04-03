<?php

namespace App\Http\Services\Variant;

use App\Http\Contracts\VariantServiceInterface;
use App\Http\Services\Material\MaterialService;
use App\Models\MaterialUnitValueVariants;
use App\Models\OptionValueVariants;
use App\Models\Product;
use App\Models\Variant;

class  VariantService implements VariantServiceInterface
{
    protected const EMPTY_OPTIONS = 'empty_options';
    protected const NEW_VALUE_ALREADY_EXISTS = 'new_value_already_exists';
    protected const VARIANT_WITH_OPTIONS_ALREADY_EXISTS = 'variant_with_options_already_exists';

    public function validateVariantWhenCreate($valuesForBind, $newValues, $newOptions, $product)
    {

        //Ошибка если массивы пустые
        $isError = $this->validateIfEmpty($valuesForBind, $newValues, $newOptions, $product);
        if ($isError) return self::EMPTY_OPTIONS;;

        //Ошибка если в списке новых значений есть то которое уже существет
        $isError = $this->validateNewValues($newValues, $product);
        if ($isError) return self::NEW_VALUE_ALREADY_EXISTS;;

        // Ошибка если существет кандидат при биндинге без новых значений
        $isError = $this->validateIfVariantWithSameOptionsExists($valuesForBind, $newValues, $product);
        if ($isError) return self::VARIANT_WITH_OPTIONS_ALREADY_EXISTS;

        return null;
    }

    public function validateIfVariantWithSameOptionsExists($valuesForBind, $newValues, $product)
    {
        if (isset ($newValues) && count($newValues) < 1) {
            $productVariants = $product->variants;
            foreach ($productVariants as $productVariant) {
                $productVariantOptionValues = $productVariant->option_values;
                $optionValuesIds = [];
                foreach ($productVariantOptionValues as $productVariantOptionValue) {
                    $optionValuesIds[] = $productVariantOptionValue->id;
                }
                $different = array_diff($optionValuesIds, $valuesForBind);

                if (count($different) < 1) {
                    return 1;
                }
            }
        }
        return false;
    }

    public function validateIfEmpty($valuesForBind, $newValues, $newOptions, $product)
    {
        if ((
                !isset($valuesForBind) ||
                count($valuesForBind) < 1
            ) && (
                !isset($newValues) ||
                count($newValues) < 1
            ) && (count($product->option_names) > 0)
        ) {
            return true;
        }
        return false;
    }

    public function validateNewValues($newValues, $product) {

//        if (isset($newValues)) {
//            $productOptionValues = $product->option_values;
//            foreach ($productOptionValues as $productOptionValue) {
//                foreach ($newValues as $newValue) {
//                    if ($newValue['name_id'] === $productOptionValue->option_name_id && $productOptionValue->title === $newValue['value']) {
//                        return true;
//                    }
//                }
//            }
//        }
        return false;
    }

    public function aggregateVariantByNameValues(&$variant)
    {
        $product = Product::with(['variants' => fn ($query) => $query->with('material_unit_values'), 'materials' => fn ($query) => $query->with('main_material_unit', 'material_units')])->findOrFail($variant->product_id);
        $variant->product = $product;

//        $productValues = $product->option_values;
        $productValues = collect([]);
        $productVars = $product->variants;
        foreach ($productVars as $productVar) {
            $varVals = $productVar->material_unit_values;
            foreach ($varVals as $val) {
                if ($productValues->search(fn($value) => $value->id === $val->id) === false) {
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
                        if ($unit->id == $value->material_unit_id) {
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
                            if (+$existsVariantValue->id == +$nameValue->id) {
                                $flag = true;
                                $searchedValue = $existsVariantValue;
                                break 2;
                            }
                        }
                    }
                    if ($flag) {
                        array_push($variantValues, $searchedValue);
                        $materialUnit->activeValueId = $searchedValue->id;
                    }
                }
//                $variant->variantValues = $variantValues;
//
//                $variantValues = $variant->material_unit_values;
//                $variant->variantValues = $variantValues;
                /////////////////////////////////////////////////////////////////////////////////////////////////////////


                //Обозначаю активные элементы
                foreach($productValues as $productValue) {
                    foreach($variantValues as $variantValue) {
                        if ($productValue->id == $variantValue->id) {
                            $productValue->active = true;
                        }
                    }
                }
                $activeIds = [];

                //фильтрация
                foreach ($materialUnits as $key=>$materialUnit) {
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

                    $searchedVariantIds = $this->intersectVariants($activeIds, $product->id);
                    $materialUnit->searchedVariantIds = $searchedVariantIds;

                    //Определяем все варианты на текущей итерации у которых до текущей итерации опции совпадают
                    $vals = [];
                    foreach($searchedVariantIds as $searchedVariantId) {

                        $variantValuesNotes = MaterialUnitValueVariants::where('variant_id', $searchedVariantId)->get();
                        foreach($variantValuesNotes as $variantValuesNote) {
                            array_push($vals, $variantValuesNote->material_unit_value_id);
                        }
                        $result = [];
                        foreach($vals as $key=>$val) {
                            if (!in_array($val, $result)) {
                                $result[$key] = $val;
                            }
                        }
                    }

                    $availableValues = [];
                    foreach ($vals as $val) {
                        foreach($materialUnit->values as $mtVal) {
                            if ($mtVal->id == $val) {
                                if (!in_array($mtVal, $availableValues)) {
                                    array_push($availableValues, $mtVal);
                                }
                            }
                        }
                    }
                    $materialUnit->values = $availableValues;

                    //Попробую здесь построить ссылку важный момент здесь selected ids это предыдущие только
                    $selectedIds = [];
                    foreach($variantValues as $variantValue) {
                        if ($materialUnit->id == $variantValue->material_unit_id) {
                            break;
                        } else {
                            array_push($selectedIds, $variantValue->id);
                        }
                    }

                    $materialUnit->selectedIds = $selectedIds;

                    foreach($materialUnit->values as $value) {
                        $searchedIds = [...$selectedIds, $value->id];
                        //Над этим я пыхтел 2 недели!!! Относиться осторожно!!!
                        $bingoVariantArray = $this->intersectVariants($searchedIds, $product->id);
                        $bingoVariant = null;
                        foreach($bingoVariantArray as $key=>$var) {
                            $bingoVariant = $var;
                            break;
                        }
                        if (isset($bingoVariant)) {
                            $value->linkedVariantId = $bingoVariant;
                        }
                        $value->searchedIds = $searchedIds;
                    }
                    array_push($activeIds, $materialUnit->activeValueId);
                }
                // Построение ссылки на вариант для первой опции
                $firstOptionName = $variant->material_units[0];
                foreach ($firstOptionName->values as $nameValue) {
                    $searchedVariantNote = MaterialUnitValueVariants::where('material_unit_value_id', $nameValue->id)->first();
                    $searchedVariant = Variant::where('id', $searchedVariantNote->variant_id)->first();
                    $nameValue->linkedVariantId = $searchedVariant->id;
                }
            }
        }
        return $variant;
    }

    public function intersectVariants(array $valuesIds, $productId) {
        $resultIds = [];
        foreach($valuesIds as $key=>$value) {
            array_push($resultIds, +$value);
        }
        $candidates = [];
        $notes = MaterialUnitValueVariants::query()->whereRelation('variant', 'product_id', $productId)->get();
        foreach($resultIds as $materialValue) {
            $$materialValue = [];
            foreach($notes as $note) {
                if ($note->material_unit_value_id == $materialValue) {
                    array_push($$materialValue, $note->variant_id);
                }
            }
            array_push($candidates, $$materialValue);
        }
        return array_intersect(...$candidates);
    }
}
