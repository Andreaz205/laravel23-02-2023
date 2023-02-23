<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSales;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:sale list', ['only' => ['index', 'show']]);
        $this->middleware('can:sale create', ['only' => ['create', 'store']]);
        $this->middleware('can:sale edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:sale delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $sales = Sale::latest()->get();
        return inertia('Sale/Index', [
            'data' => $sales,
            'can-sales' => [
                'list' => Auth('admin')->user()?->can('sale list'),
                'create' => Auth('admin')->user()?->can('sale create'),
                'edit' => Auth('admin')->user()?->can('sale edit'),
                'delete' => Auth('admin')->user()?->can('sale delete'),
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:sales,title'
        ]);

        return Sale::create($data);
    }

    public function show(Sale $sale)
    {
        return inertia('Sale/Show', [
            'data' => $sale,
            'can-sales' => [
                'list' => Auth('admin')->user()?->can('sale list'),
                'create' => Auth('admin')->user()?->can('sale create'),
                'edit' => Auth('admin')->user()?->can('sale edit'),
                'delete' => Auth('admin')->user()?->can('sale delete'),
            ]
        ]);
    }

    public function setImage(Sale $sale, Request $request)
    {
        $data = $request->validate([
            'image' => 'required|file'
        ]);
        $image = $data['image'];

        $name = md5( Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);

        $sale->update([
            'image_path' => $filePath,
            'image_url' => url('/storage/' . $filePath),
        ]);
        return $sale;
    }

    public function deleteImage(Sale $sale) {
        Storage::delete($sale->image_path);
        $sale->update(['image_url' =>  null]);
        return response()->json(['status' => 'success']);
    }

    public function saleProducts(Sale $sale)
    {
        $products = Product::all();
        if (isset($products)) {
//            $productIds = ProductSales::where('sale_id', $sale->id)->get();
//            dd($productIds);
            $saleProducts = $sale->getProductsAttribute($sale);
            foreach ($products as $product) {
                foreach ($saleProducts as $saleProduct) {
                    if ($product->id === $saleProduct->id) {
                        $product->is_exists = true;
                    }
                }
            }
        }

        return $products;
    }

    public function toggleProductExists(Sale $sale, Product $product)
    {
        $candidateProductSalesNote = ProductSales::where('product_id', $product->id)->where('sale_id', $sale->id)->first();
        if (isset($candidateProductSalesNote)) {
            $candidateProductSalesNote->delete();
        } else {
            ProductSales::create([
                'product_id' => $product->id,
                'sale_id' => $sale->id,
            ]);
        }
        return response()->json(['status' => 'success']);
    }

    public function publish(Sale $sale)
    {
        $sale->update(['is_public' => !$sale->is_public]);
        return response()->json(['status' => 'success']);
    }
}
