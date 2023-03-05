<?php

namespace App\Http\Controllers\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\Price\StoreRequest;
use App\Http\Requests\Price\UpdateRequest;
use App\Models\Group;
use App\Models\Price;
use App\Models\PriceVariants;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PriceController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:price list', ['only' => ['index', 'show']]);
        $this->middleware('can:banner create', ['only' => ['create', 'store']]);
        $this->middleware('can:banner edit', ['only' => ['edit', 'update', 'order']]);
        $this->middleware('can:banner delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $freeGroups = Group::whereNull('price_id')->get();
        $prices = Price::with('groups')->get();
        return inertia('Price/Index', [
            'free-groups' => $freeGroups,
            'prices' => $prices,
            'can-prices' => [
                'list' => Auth('admin')->user()?->can('price list'),
                'create' => Auth('admin')->user()?->can('price create'),
                'edit' => Auth('admin')->user()?->can('price edit'),
                'delete' => Auth('admin')->user()?->can('price delete'),
            ]
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $groups = $data['groups'];



        unset($data['groups']);
        try {
            DB::beginTransaction();
            $price = Price::create($data);
            $groups = Group::whereIn('id', $groups)->get();
            $price->groups()->saveMany($groups);
            $variantsIds = Variant::pluck('id')->toArray();
            $prices = [];
            foreach ($variantsIds as $variantId) {
                $prices[] = [
                    'variant_id' => $variantId,
                    'price_id' => $price->id,
                    'price' => 0,
                ];
            }

            PriceVariants::query()->insert($prices);

            Db::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error], 400);
        }

        return $price->load('groups');
    }

    public function update(UpdateRequest $request, PriceVariants $pivot)
    {
        $data = $request->validated();
        $pivot->update(['price' => $data['price']]);
        return $pivot;
    }

    public function destroy(Price $price)
    {
        $price->delete();
        return Response::json(['status' => 'Deleted successfully!']);
    }

}
