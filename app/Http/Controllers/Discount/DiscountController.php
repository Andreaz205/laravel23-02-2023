<?php

namespace App\Http\Controllers\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\StoreAccumulativeDiscountRequest;
use App\Http\Requests\Discount\StoreRequest;
use App\Http\Requests\Discount\ToggleAvailabilityRequest;
use App\Http\Resources\Category\CategoryResource;
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
        $categories = Category::query()->with('child_categories')->get();
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

    public function storeAccumulative(StoreAccumulativeDiscountRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 'accumulative';
        if (isset($data['groups'])) {
            $requestedGroups = $data['groups'];
            unset($data['groups']);
        }
        if (isset($data['categories'])) {
            $requestedCategories = $data['categories'];
            unset($data['categories']);
        }
        try {
            DB::beginTransaction();
            $discount = Discount::create($data);
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
        return $discount;
    }
}
