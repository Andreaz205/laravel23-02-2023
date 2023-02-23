<?php

namespace App\Http\Contracts;

interface VariantServiceInterface
{
    public function validateVariantWhenCreate($valuesForBind, $newValues, $newOptions, $product);
    public function validateIfEmpty($valuesForBind, $newValues, $newOptions, $product);
    public function validateNewValues($newValues, $product);
    public function validateIfVariantWithSameOptionsExists($valuesForBind, $newValues, $product);

}
