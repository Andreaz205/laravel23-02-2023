<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Services\Image\UploadImageService;
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
        $sales = Sale::with(['products'], fn ($query) => $query->limit(10))->withCount('products')->latest()->get();
        return inertia('Sale/Index', [
            'sales' => $sales,
            'can-sales' => [
                'list' => Auth('admin')->user()?->can('sale list'),
                'create' => Auth('admin')->user()?->can('sale create'),
                'edit' => Auth('admin')->user()?->can('sale edit'),
                'delete' => Auth('admin')->user()?->can('sale delete'),
            ]
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        return Sale::create($data);
    }

    public function show(Sale $sale)
    {
        $sale->load('products');
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

    public function setImage(Sale $sale, Request $request, UploadImageService $uploadImageService)
    {
        $data = $request->validate([
            'image' => 'required|file'
        ]);
        $image = $data['image'];
        $data = $uploadImageService->upload($image);
        $sale->update([
            'image_path' => $data['path'],
            'image_url' => $data['url'],
        ]);
        return $sale;
    }

    public function deleteImage(Sale $sale) {
        Storage::delete($sale->image_path);
        $sale->update(['image_url' =>  null, 'image_path' => null]);
        return response()->json(['status' => 'success']);
    }

    public function saleProducts(Sale $sale)
    {
        $products = Product::all();
        if (isset($products)) {
            $saleProducts = $sale->products()->get();
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

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect('/admin/sales')->withMessage('Акция '. $sale->title. ' успешно удалена!');
    }
}
