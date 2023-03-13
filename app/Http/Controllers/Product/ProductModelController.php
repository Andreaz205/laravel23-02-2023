<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductModel\StoreImageRequest;
use App\Http\Requests\ProductModel\StoreRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\ProductModelImage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductModelController extends Controller
{
    public function edit(Product $product)
    {
        $models = $product->product_models()->get();
        return inertia('Product/Model/Edit', [
            'models' => $models,
            'product' => $product,
            'can-products' => [
                'list' => Auth('admin')->user()->can('product list'),
                'create' => Auth('admin')->user()->can('product create'),
                'edit' => Auth('admin')->user()->can('product edit'),
                'delete' => Auth('admin')->user()->can('product delete'),
            ]
        ]);
    }

    public function store(Product $product, StoreRequest $request)
    {
        $data = $request->validated();
        $model = $product->product_models()->create($data);
        return $model->load('images');
    }

    /**
     * @throws ValidationException
     */
    public function addImage(Product $product, ProductModel $model, StoreImageRequest $request, UploadImageService $service)
    {
        $data = $request->validated();
        $count = $model->images()->count();
        if ($count >= 32) throw ValidationException::withMessages(['Нельзя добавить более 32 изображений товару!']);
        $data = $service->upload($data['image']);

        return ProductModelImage::create([
            'product_model_id' => $model->id,
            'image_url' => $data['url'],
            'image_path' => $data['path'],
        ]);
    }

    public function deleteImage(Product $product, ProductModel $model, ProductModelImage $image)
    {
        $image->delete();
        return 11111;
    }

    public function destroy(Product $product, ProductModel $productModel)
    {
        $productModel->delete();
        return 1111;
    }

}

