<?php

namespace App\Http\Controllers\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\StoreAccumulativeDiscountRequest;
use App\Http\Requests\Discount\StoreRequest;
use App\Http\Requests\Discount\ToggleAvailabilityRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Discount\AccumulativeDiscountResource;
use App\Http\Services\Discount\DiscountService;
use App\Models\Category;
use App\Models\Discount;
use App\Models\DiscountsAvailability;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:room list', ['only' => ['index', 'show']]);
        $this->middleware('can:room create', ['only' => ['create', 'store']]);
        $this->middleware('can:room edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:room delete', ['only' => ['destroy']]);
    }

    public function index(DiscountService $service)
    {
        $discounts = $service->getDiscountsWithAvailability();
        $categories = Category::query()->whereNull('parent_category_id')->with('child_categories')->get();
        $groups = Group::all();
        return inertia('Discount/Index', [
            'discountsData' => $discounts,
            'categoriesData' => CategoryResource::collection($categories),
            'groupsData' => $groups,
            'can-discount' => [
                'list' => Auth('admin')->user()?->can('discount list'),
                'create' => Auth('admin')->user()?->can('discount create'),
                'edit' => Auth('admin')->user()?->can('discount edit'),
                'delete' => Auth('admin')->user()?->can('discount delete'),
            ]
        ]);
    }

    public function toggle(ToggleAvailabilityRequest $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $isAvailableNow = DiscountsAvailability::where('type', $type)->first();
        $isAvailableNow->update(['is_available' => !$isAvailableNow->is_available]);
        return Response::json(['status' => 'success', 'is_available' => $isAvailableNow->is_available]);
    }

    public function storeAccumulativeDiscount(StoreAccumulativeDiscountRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 'accumulative';
        $availableGroups = 'all';
        if (isset($data['groups'])) {
            if ($data['groups'] === 'without_groups') {
                $availableGroups = 'without_groups';
            } else {
                $availableGroups = 'selected';
                $requestedGroups = $data['groups'];
            }
            unset($data['groups']);
        }
        if (isset($data['categories'])) {
            $requestedCategories = $data['categories'];
            unset($data['categories']);
        }

        try {
            DB::beginTransaction();
            $discount = Discount::create([...$data, 'available_groups' => $availableGroups]);
            if (isset($requestedCategories)) {
                $discount->categories()->attach($requestedCategories);
            }
            if (isset($requestedGroups)) {
                $discount->groups()->attach($requestedGroups);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error->getMessage()]);
        }
        return new AccumulativeDiscountResource($discount);
    }

    //Здесь я повторяюсь в коде
    public function storeOrderDiscount(StoreAccumulativeDiscountRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 'order';
        $availableGroups = 'all';
        if (isset($data['groups'])) {
            if ($data['groups'] === 'without_groups') {
                $availableGroups = 'without_groups';
            } else {
                $availableGroups = 'selected';
                $requestedGroups = $data['groups'];
            }
            unset($data['groups']);
        }
        if (isset($data['categories'])) {
            $requestedCategories = $data['categories'];
            unset($data['categories']);
        }

        try {
            DB::beginTransaction();
            $discount = Discount::create([...$data, 'available_groups' => $availableGroups]);
            if (isset($requestedCategories)) {
                $discount->categories()->attach($requestedCategories);
            }
            if (isset($requestedGroups)) {
                $discount->groups()->attach($requestedGroups);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error->getMessage()]);
        }
        return new AccumulativeDiscountResource($discount);
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return Response::json(['status' => 'success']);
    }
}
