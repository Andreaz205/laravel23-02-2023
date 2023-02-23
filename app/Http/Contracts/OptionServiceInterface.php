<?php

namespace App\Http\Contracts;

interface OptionServiceInterface
{
    public function validateOptionBeforeBind($option, $variant, $product);
}
