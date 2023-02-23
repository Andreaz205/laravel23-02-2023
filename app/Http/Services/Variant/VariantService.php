<?php

namespace App\Http\Services\Variant;

use App\Http\Contracts\VariantServiceInterface;

class VariantService implements VariantServiceInterface
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

        if (isset($newValues)) {
            $productOptionValues = $product->option_values;
            foreach ($productOptionValues as $productOptionValue) {
                foreach ($newValues as $newValue) {
                    if ($newValue['name_id'] === $productOptionValue->option_name_id && $productOptionValue->title === $newValue['value']) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
