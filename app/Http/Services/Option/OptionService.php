<?php

namespace App\Http\Services\Option;

use App\Http\Contracts\OptionServiceInterface;

class OptionService implements OptionServiceInterface
{
    public function validateOptionBeforeBind($option, $variant, $product)
    {
        $optionNameId = $option['option_name_id'];
        $optionValueId = $option['option_value_id'];

    }
}
