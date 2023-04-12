<?php

namespace App\Http\Services\Discount;

use App\Models\Discount;
use App\Models\DiscountsAvailability;
use App\Models\Group;

class DiscountService
{
    public function getDiscountsWithAvailability()
    {
        $discounts = Discount::with('groups', 'categories')->get();

        $orderDiscounts = [];
        $groupDiscounts = Group::with('discounted_categories')->get();
        $couponDiscounts = [];

        foreach ($discounts as $discount) {
            if ($discount->type === 'order') $orderDiscounts[] = $discount;
            if ($discount->type === 'coupon') $couponDiscounts[] = $discount;
        }

        $orderDiscountsResult = [];
        $orderDiscountsResult['discounts'] = $orderDiscounts;

        $groupDiscountsResult = [];
        $groupDiscountsResult['discounts'] = $groupDiscounts;

        $couponDiscountsResult = [];
        $couponDiscountsResult['discounts'] = $couponDiscounts;


        $discountsAvailability = DiscountsAvailability::limit(3)->get();
        foreach ($discountsAvailability as $accessItem) {

            if ($accessItem->type == 'order') {
                $orderDiscountsResult['is_available'] = $accessItem->is_available;
            }
            if ($accessItem->type == 'group') {
                $groupDiscountsResult['is_available'] = $accessItem->is_available;
            }
            if ($accessItem->type == 'coupon') {
                $couponDiscountsResult['is_available'] = $accessItem->is_available;
            }
        }

        $result = [
            'order_discounts' => $orderDiscountsResult,
            'group_discounts' => $groupDiscountsResult,
            'coupon_discounts' => $couponDiscountsResult,
        ];
        return $result;
    }

}
