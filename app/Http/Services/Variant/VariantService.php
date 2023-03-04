<?php

namespace App\Http\Services\Variant;

use App\Http\Contracts\VariantServiceInterface;
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
        $product = Product::findOrFail($variant->product_id);
        $variant->product = $product;

        $productValues = $product->option_values;
        if (isset($product->option_names)) {
            $optionNames = $product->option_names;

            $optValues = [];
            if (isset($productValues)) {
                foreach ($optionNames as $name) {
                    foreach ($productValues as $value) {

                        if ($name->id == $value->option_name_id) {
                            $optValues[] = $value;
                        }
                    }
                    $name->values = $optValues;
                    $optValues = [];
                }
                $variant->optionNames = $optionNames;
                //Это сложная херня находит опции значения которые есть у варианта с учетом дефолтных значений
                $existsVariantValues = $variant->optionValues;

                $variantValues = [];
                foreach ($optionNames as $optionName) {
                    $flag = false;
                    $searchedValue = null;
                    foreach ($optionName->values as $nameValue) {
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
                        $optionName->activeValueId = $searchedValue->id;
                    } else {
                        $searchedValue = $optionName->default_option_value;
                        array_push($variantValues, $searchedValue);
                        $optionName->activeValueId = $searchedValue->id;
                    }
                }
                $variant->variantValues = $variantValues;
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
                foreach ($optionNames as $key=>$optionName) {

                    if ($key ==0) {
                        array_push($activeIds, $optionName->activeValueId);
                        continue;
                    }

                    $searchedVariantIds = $this->intersectVariants($activeIds);
                    $optionName->searchedVariantIds = $searchedVariantIds;

                    //Определяем все варианты на текущей итерации у которых до текущей итерации опции совпадают
                    $vals = [];
                    foreach($searchedVariantIds as $searchedVariantId) {

                        $variantValuesNotes = OptionValueVariants::where('variant_id', $searchedVariantId)->get();
                        foreach($variantValuesNotes as $variantValuesNote) {
                            array_push($vals, $variantValuesNote->option_value_id);
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
                        foreach($optionName->values as $opVal) {
                            if ($opVal->id == $val) {
                                if (!in_array($opVal, $availableValues)) {
                                    array_push($availableValues, $opVal);
                                }
                            }
                        }
                    }
                    $optionName->values = $availableValues;

                    //Попробую здесь построить ссылку важный момент здесь selected ids это предыдущие только
                    $selectedIds = [];
                    foreach($variantValues as $variantValue) {
                        if ($optionName->id == $variantValue->option_name_id) {
                            break;
                        } else {
                            array_push($selectedIds, $variantValue->id);
                        }

                    }

                    $optionName->selectedIds = $selectedIds;

                    foreach($optionName->values as $value) {
                        $searchedIds = [...$selectedIds, $value->id];
                        //Над этим я пыхтел 2 недели!!! Относиться осторожно!!!
                        $bingoVariantArray = $this->intersectVariants($searchedIds);
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

                    array_push($activeIds, $optionName->activeValueId);
                }

                // Построение ссылки на вариант для первой опции
                $firstOptionName = $variant->optionNames[0];
                foreach ($firstOptionName->values as $nameValue) {
                    $searchedVariantNote = OptionValueVariants::where('option_value_id', $nameValue->id)->first();
                    $searchedVariant = Variant::where('id', $searchedVariantNote->variant_id)->first();
                    $nameValue->linkedVariantId = $searchedVariant->id;
                }
            }

        }
        return $variant;
    }

    public function intersectVariants(array $valuesIds) {
        $resultIds = [];
        foreach($valuesIds as $key=>$value) {
            array_push($resultIds, +$value);
        }

        $candidates = [];

        $notes = OptionValueVariants::all();
        foreach($resultIds as $optionValue) {
            $$optionValue = [];
            foreach($notes as $note) {
                if ($note->option_value_id == $optionValue) {
                    array_push($$optionValue, $note->variant_id);
                }
            }
            array_push($candidates, $$optionValue);
        }

        $result = array_intersect(...$candidates);

        return  $result;
    }
}
