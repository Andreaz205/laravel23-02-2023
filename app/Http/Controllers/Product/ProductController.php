<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AppendSizeRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Services\Category\CategoryService;
use App\Http\Services\Material\MaterialService;
use App\Http\Services\Product\ProductService;
use App\Models\AccentProperty;
use App\Models\AdditionalProductSize;
use App\Models\Category;
use App\Models\Group;
use App\Models\OptionName;
use App\Models\Parameter;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected CategoryService $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $products = Product::with('images')->withCount('variants')->orderByDesc('created_at')->paginate(20);
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

    public function byTerm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term' => 'nullable|string|max:255'
        ]);
        if ($validator->fails()) {
            return Response::json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        if (isset($data['term']))
            $products = Product::where('title', 'ilike', '%'.$data['term'].'%')->with('images')->withCount('variants')->paginate(20);
        else
            $products =  Product::with('images')->withCount('variants')->paginate(20);

        foreach ($products as $product) {
            $product->min_max_price = $product->getMinMaxPriceAttribute();
            $product->quantity = $product->getQuantityAttribute();
        }
        return $products;
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

    public function show(Product $product, MaterialService $materialService)
    {
//        $product = $this->productService->aggregateOptionsForSingleProduct($product);
        $accentProperties = AccentProperty::with('media')->get();

//        $productNames = $product->option_names()->with('option_values')->get();
//        $product->option_names = $productNames;
//        $allOptionNames = OptionName::query()->with('option_values')->get();
        $models = $product
            ->product_models()
            ->withCount('images')
            ->get()
            ->map(function ($model) {
                $model->setRelation('images', $model->images->take(6));
                return $model;
            });

//        $productNames = $product->option_names()->with('option_values')->get();
//        $product->option_names = $productNames;
//        $allOptionNames = OptionName::query()->with('option_values')->get();
//        $models = $product->product_models()->with(['images'])->withCount('images')->get();

        $materials = $product->materials()->with(['material_units' => fn ($query) => $query->with(['values'])])->get();
        $materialSets =  $materialService->getSets($materials);
        $product->load('parameters', 'category');
        $product->accent_properties = $product->accent_properties()->with('media')->get();
        $product->images = $product->images()->orderBy('position', 'ASC')->get();
        $product->variants = $product->variants()->with([ 'images', 'prices', 'material_unit_values' => fn ($query) => $query->with('color')])->get();
        $categories = Category::all();
//        $categories = $this->productService->categoriesWithCheckedProp($product);
        $categories = $this->categoryService->nestedCategories($categories);
        $prices = Price::all();
//            'allOptionNames' => $allOptionNames,
//            'categoriesData' => $categories,

        return inertia('Product/Show', [
            'material_sets' => $materialSets,
            'models' => $models,
            'accentPropertiesProps' => $accentProperties,
            'prices' => $prices,
            'productData' => $product,
            'categories' => $categories,
            'materials' => $materials,
            'can-products' => [
                'list' => Auth('admin')->user()->can('product list'),
                'create' => Auth('admin')->user()->can('product create'),
                'edit' => Auth('admin')->user()->can('product edit'),
                'delete' => Auth('admin')->user()->can('product delete'),
            ]
        ]);
    }

    public function edit(Product $product)
    {
        $product->load('parameters', 'additional_sizes');
        return inertia('Product/Edit', [
            'productData' => $product,
            'can-products' => [
                'list' => Auth('admin')->user()->can('product list'),
                'create' => Auth('admin')->user()->can('product create'),
                'edit' => Auth('admin')->user()->can('product edit'),
                'delete' => Auth('admin')->user()->can('product delete'),
            ]
        ]);
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $data = $request->validated();
        if (isset($data['parameters'])) {
            try {
                DB::beginTransaction();
                $parameters = $data['parameters'];
                $product->parameters()->delete();
                foreach ($parameters as $parameter) {
                    Parameter::create([
                        'title' => $parameter['title'],
                        'value' => $parameter['value'],
                        'product_id' => $product->id,
                    ]);
                }
                unset($data['parameters']);
                $product->update($data);
                DB::commit();
            } catch (\Exception $error) {
                DB::rollBack();
                return Response::json(['error' => $error->getMessage()], 500);
            }
        }
        return Response::json(['status' => 'success']);
    }

    public function appendSize(AppendSizeRequest $request, Product $product)
    {
        $data = $request->validated();
        $sameTitleSize = $product->additional_sizes()->where('title', $data['title'])->first();
        if (isset($sameTitleSize))
            throw ValidationException::withMessages(['Размер с таким названием у товара ' . $product->title . ' уже существует!']);
        return $product->additional_sizes()->create($data);
    }

    public function deleteSize(AdditionalProductSize $size)
    {
        return $size->delete();
    }

    public function togglePublish(Product $product)
    {
        return $product->update(['is_published' => !$product->is_published]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        Response::json(['status' => 'success']);
    }
}
