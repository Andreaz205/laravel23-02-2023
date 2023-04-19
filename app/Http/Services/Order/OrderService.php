<?php

namespace App\Http\Services\Order;

use App\Models\Bonus;
use App\Models\Order;
use App\Models\Price;
use App\Models\User;
use App\Models\Variant;

class OrderService
{
    private array $canceledStatuses;

    public function __construct()
    {
        $this->canceledStatuses = ['aborted', 'client_enabled', 'back', 'back_process',];
    }

    public function calculateBonuses($orderedVariantsIds, $user = null): int
    {
        if (!isset($user)) return 0;
        $variants = Variant::query()->whereIn('id', $orderedVariantsIds)->with(['product' => fn ($query) => $query->with('category'), 'kits'])->get();
        $bonusesInfo = Bonus::query()->with(['categories', 'groups'])->first();
        $isUserGroupAvailableForBonuses = $this->checkUserGroupAvailabilityForBonuses($bonusesInfo, $user);

        $userGroup = $user?->group;
        $bonusesCount = 0;
        if ($isUserGroupAvailableForBonuses) {

            if (isset($userGroup)) {
                $isHasPrice = $this->hasUserPriceByGroup($user);
                if ($isHasPrice) {
                    $this->attachUserPriceByGroup($variants, $user);
                } else {
                    $discount = $user->group->discount;
                    $this->attachUserPriceByGroupDiscount($variants, $discount);
                }
            }

            foreach ($variants as $variant) {
                $isCategoryAvailableForBonuses = $this->checkVariantCategoryAvailabilityForBonuses($variant, $bonusesInfo);
                if ($isCategoryAvailableForBonuses) {
                    if ($bonusesInfo->allow_kits) {
                        if (count($variant->kits)) {
                            if ($bonusesInfo->allow_discounted) {
                                $bonusesCount += $this->getBalls($variant->price, $bonusesInfo);
                            } else {
                                if (!$variant->old_price) {
                                    $bonusesCount += $this->getBalls($variant->price, $bonusesInfo);
                                }
                            }
                        }
                    } else {
                        if ($bonusesInfo->allow_discounted) {
                            $bonusesCount += $this->getBalls($variant->price, $bonusesInfo);
                        } else {
                            if (!$variant->old_price) {
                                $bonusesCount += $this->getBalls($variant->price, $bonusesInfo);
                            }
                        }
                    }
                }
            }
        }

        return $bonusesCount;
    }

    public function attachUserPriceByGroup(&$variants, $user = null): void
    {
        $price = Price::query()->whereRelation('groups', 'id', '=', $user->group->id)->first();
        $variants = Variant::query()
            ->whereIn('id', $variants->map(fn ($variant) => $variant->id))
            ->with(['prices' => fn ($query) => $query->where('price_id', $price->id)])
            ->get();
        foreach ($variants as $variant) {
            $variant['price'] = $variant->prices[0]->price;
        }
    }

    public function attachUserPriceByGroupDiscount(&$variants, $discount): void
    {
        foreach ($variants as $variant) {
            $variant['price'] = $variant->price + $variant->price * $discount / 100;
        }
    }

    public function hasUserPriceByGroup($user = null): bool
    {
        $price = Price::query()->whereRelation('groups', 'id', '=', $user->group->id)->first();
        return isset($price);
    }

    public function getBalls(int $price, \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null $bonusInfo): int
    {
        $coefficientConversion = $bonusInfo->coefficient_conversion;
        $percent = $bonusInfo->bonus_percent;
        return $price * $percent / 100 * $coefficientConversion ?? 0;
    }

    public function checkVariantCategoryAvailabilityForBonuses(&$variant, \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null &$bonusInfo): bool
    {
        if ($bonusInfo->available_groups === 'all') return true;
        $variantCategory = $variant->product->category;
        if (!count($bonusInfo->categories)) return true;
        $isFound = $bonusInfo->categories->search(fn ($item) => (int)$item->id === (int)$variantCategory->id);
        return ! $isFound === false;

    }

    public function checkUserGroupAvailabilityForBonuses(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null &$bonusInfo, \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null &$user = null): bool
    {
        if ($bonusInfo->available_groups === 'all') return true;
        if ($bonusInfo->available_groups === 'without_groups') {
            $group = $user?->group()->get();
            return ! isset($group) && count($group);
        }
        $bonusGroups = $bonusInfo->groups;
        if (isset($bonusGroups)) {
            $group = $user?->group()->get();
            if (isset($group) && count($group)) {
                $isFound = $bonusGroups->search(fn ($item) => (int)$item->id === (int)$group[0]->id);
                return ! $isFound === false;
            }
            return false;
        }
        return true;
    }

    public function handleEditBonusesAfterChangeStatus(string $status, Order $order): void
    {
        if (isset($order->user) && $order->is_payed) {
            $orderBonuses = $order->bonuses;
            if (in_array($status, $this->canceledStatuses)) {
                if ($order->is_bonuses_added) {
                    $order->user->update(['bonuses' => $order->user->bonuses - $orderBonuses]);
                    $order->update(['is_bonuses_added' => false]);
                }
            } else {
                if (! $order->is_bonuses_added) {
                    $order->user->update(['bonuses' => $order->user->bonuses + $orderBonuses]);
                    $order->update(['is_bonuses_added' => true]);
                }
            }
        }
    }

    public function handleEditBonusesAfterChangePaidStatus(bool $isPaid, Order $order): void
    {
        $user = $order->user;
        if (isset($user)) {
            if ($isPaid) {
                if (! $order->is_bonuses_added) {
                    $user->update(['bonuses' => $user->bonuses + $order->bonuses]);
                    $order->update(['is_bonuses_added' => true]);
                }
            } else {
                if ($order->is_bonuses_added) {
                    $user->update(['bonuses' => $user->bonuses - $order->bonuses]);
                    $order->update(['is_bonuses_added' => false]);
                }
            }
        }
    }

    public function getOrderSumFromRequestWithPreparedPrices(\Illuminate\Support\Collection $orderedVariantsArray, \Illuminate\Database\Eloquent\Collection $variants): array
    {
        $purchaseSum = 0;
        $sum = 0;
        foreach ($orderedVariantsArray as $orderedVariantsArrayItem) {
            $variant = $variants->find($orderedVariantsArrayItem['id']);
            $sum += $variant->price;
            $purchaseSum += $variant->purchase_price;
        }
        return [
            'sum' => $sum,
            'purchase_sum' => $purchaseSum,
        ];
    }
}
