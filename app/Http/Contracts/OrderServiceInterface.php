<?php

namespace App\Http\Contracts;

interface OrderServiceInterface
{
    public function checkPrice($orderedVariantsArray);
}
