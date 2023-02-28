<?php

namespace App\Http\Services\Discount;

use App\Models\Discount;
use App\Models\DiscountsAvailability;

class DiscountService
{
    public function getDiscountsWithAvailability()
    {
        $discounts = Discount::all();

        $accumulativeDiscounts = [];
        $orderDiscounts = [];
        $groupDiscounts = [];
        $couponDiscounts = [];

        $accumulativeDiscountsResult = [];
        $accumulativeDiscountsResult['discounts'] = $accumulativeDiscounts;

        $orderDiscountsResult = [];
        $orderDiscountsResult['discounts'] = $orderDiscounts;

        $groupDiscountsResult = [];
        $groupDiscountsResult['discounts'] = $groupDiscounts;

        $couponDiscountsResult = [];
        $couponDiscountsResult['discounts'] = $couponDiscounts;

        foreach ($discounts as $discount) {
            if ($discount->type === 'accumulative') $accumulativeDiscounts[] = $discount;
            if ($discount->type === 'order') $orderDiscounts[] = $discount;
            if ($discount->type === 'group') $groupDiscounts[] = $discount;
            if ($discount->type === 'coupon') $couponDiscounts[] = $discount;
        }

        $discountsAvailability = DiscountsAvailability::limit(4)->get();
        foreach ($discountsAvailability as $accessItem) {
            if ($accessItem->type === 'accumulative') {
                $accumulativeDiscountsResult['is_available'] = $accessItem->is_available;
            }
            if ($accessItem->type === 'accumulative') {
                $orderDiscountsResult['is_available'] = $accessItem->is_available;
            }
            if ($accessItem->type === 'accumulative') {
                $orderDiscountsResult['is_available'] = $accessItem->is_available;
            }
            if ($accessItem->type === 'accumulative') {
                $orderDiscountsResult['is_available'] = $accessItem->is_available;
            }
        }

        $result = [
            'accumulative_discounts' => $accumulativeDiscountsResult,
            'order_discounts' => $orderDiscountsResult,
            'group_discounts' => $couponDiscountsResult,
            'coupon_discounts' => $groupDiscountsResult,
        ];
        return $result;
    }
}
