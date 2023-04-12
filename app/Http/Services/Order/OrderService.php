<?php

namespace App\Http\Services\Order;

use App\Models\Variant;

class OrderService
{
    public function checkPrice($orderedVariantsArray)
    {
        $ids = [];
        foreach ($orderedVariantsArray as $arrayItem) {
            $ids[] = $arrayItem['id'];
        }
        $variants = Variant::whereIn('id', $ids)->get(['id', 'price']);
        foreach ($variants as $variant) {
            foreach ($orderedVariantsArray as $arrayItem) {
                if ($variant->id == $arrayItem['id']) {
                    if ($variant->price != $arrayItem['price']) {
                        return 'fail';
                    }
                    continue 2;
                }
            }
        }
        return 'success';
    }

    public function calculateSumAfterSales($sum, $variants, $user = null)
    {

    }
}
