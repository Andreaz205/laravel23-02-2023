<?php

namespace App\Http\Controllers\Kit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kit\StoreKitRequest;
use App\Http\Resources\Kit\KitResource;
use App\Models\Kit;
use App\Models\KitProducts;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class KitsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $kits = Kit::paginate(20);
        return inertia('Kit/Index', [
            'kitsData' => $kits,
            'can-products' => [
                'list' => Auth('admin')->user()?->can('product list'),
                'create' => Auth('admin')->user()?->can('product create'),
                'edit' => Auth('admin')->user()?->can('product edit'),
                'delete' => Auth('admin')->user()?->can('product delete'),
            ]
        ]);
    }

    public function store(StoreKitRequest $request)
    {
        $data = $request->validated();
        $newKit = Kit::create($data);
        return new KitResource($newKit);
    }

    public function edit(Kit $kit)
    {
        $products = $kit->products()->withPivot('variant_id')->with(['variants' => function ($query) {
            $query->with(['material_unit_values', 'images' => fn ($query) => $query->limit(1)]);
        }])->orderByDesc('created_at')->get();
        foreach ($products as $product) {
            $searchedVariant = Variant::with('material_unit_values')->where('id',$product->pivot->variant_id)->first();
            if (isset($searchedVariant)) {
                $searchedVariant->title = $searchedVariant->getTitleAttribute();
            }
            $product->variant = $searchedVariant;
        }

        $products->each(fn ($product) => $product->variants->each(fn($variant) => $variant->title = $variant->getTitleAttribute()));
        return inertia('Kit/Edit', [
            'products' => $products,
            'kit' => $kit,
            'can-products' => [
                'list' => Auth('admin')->user()?->can('product list'),
                'create' => Auth('admin')->user()?->can('product create'),
                'edit' => Auth('admin')->user()?->can('product edit'),
                'delete' => Auth('admin')->user()?->can('product delete'),
            ]
        ]);
    }

    public function products(Kit $kit)
    {
        $products = Product::all();
        $kitProducts =  $kit->products;
        foreach ($products as $product) {
            foreach ($kitProducts as $kitProduct) {
                if ($product->id === $kitProduct->id) {
                    $product->is_exists = true;
                    continue 2;
                }
            }
            $product->is_exists = false;
        }
        return $products;
    }

    public function toggle(Kit $kit, Product $product)
    {
        $candidate = KitProducts::where('product_id', $product->id)->where('kit_id', $kit->id)->first();
        if (isset($candidate)) {
            try {
                DB::beginTransaction();
                $candidate->delete();
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                return Response::json(['message' => $exception->getMessage()], 500);
            }

            return Response::json(['status' => 'Deleted successfully!']);
        }
        KitProducts::create([
            'kit_id' => $kit->id,
            'product_id' => $product->id
        ]);
        return Response::json(['status' => 'Created successfully!']);
    }

    public function bindVariant(Kit $kit, Variant $variant)
    {
        $variantProduct = $variant->product;
        $products = $kit->products;

        if (!$products->contains($variantProduct))
            throw ValidationException::withMessages(['Для указанного вами набора указанный вами id варианта некорректен!']);

        $pivot = KitProducts::where('product_id', $variant->product_id)->where('kit_id', $kit->id)->first();
        $pivot->variant_id = $variant->id;
        $pivot->save();
        return 11111;
    }
}
