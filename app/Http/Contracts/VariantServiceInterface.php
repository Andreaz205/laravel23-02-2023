<?php

namespace App\Http\Contracts;

interface VariantServiceInterface
{
    public function validateVariantWhenCreate($valuesForBind, $newValues, $product);
    public function validateIfEmpty($valuesForBind, $newValues);
    public function validateNewValues($newValues, $product);
    public function validateIfVariantWithSameOptionsExists($valuesForBind, $newValues, $product);
}
