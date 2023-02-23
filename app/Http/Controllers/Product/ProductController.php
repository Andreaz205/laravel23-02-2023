<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Services\Product\ProductService;
use App\Models\Group;
use App\Models\Product;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $products = Product::with('images')->withCount('variants')->paginate(20);
        foreach ($products as $product) {
            $product->min_max_price = $product->getMinMaxPriceAttribute();
            $product->quantity = $product->getQuantityAttribute();
        }

        return inertia('Product/Index', [
            'productsData' => $products,
            'can-products' => [
                'list' => Auth('admin')->user()->can('product list'),
                'create' => Auth('admin')->user()->can('product create'),
                'edit' => Auth('admin')->user()->can('product edit'),
                'delete' => Auth('admin')->user()->can('product delete'),
            ]
        ]);

    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);
        $product->min_max_price = $product->getMinMaxPriceAttribute();
        $product->quantity = 0;
        $product->variants_count = 0;
        return Response::json($product);

    }

    public function show(Product $product)
    {
        $product = $this->service->aggregateOptionsForSingleProduct($product);
        $product->images = $product->images()->orderBy('position', 'ASC')->get();
        $product->variants = $product->variants()->with(['option_values', 'images'])->get();
        $categories = $this->service->categoriesWithCheckedProp($product);
        $groups = Group::where('is_visible_in_products', true)->get();

        return inertia('Product/Show', [
            'productData' => $product,
            'categoriesData' => $categories,
            'groupsData' => $groups,
            'can-products' => [
                'list' => Auth('admin')->user()->can('product list'),
                'create' => Auth('admin')->user()->can('product create'),
                'edit' => Auth('admin')->user()->can('product edit'),
                'delete' => Auth('admin')->user()->can('product delete'),
            ]
        ]);
    }

    public function update()
    {

    }

    public function destroy(Product $product)
    {
        $product->delete();
        Response::json(['status' =>  'success']);
    }
}
