<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImage\OrderImagesRequest;
use App\Http\Requests\ProductImage\StoreImageRequest;
use App\Http\Resources\Image\ImageResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariantImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function store(StoreImageRequest $request, Product $product)
    {
        $data = $request->validated();
        $image = $data['image'];
        try {
            DB::beginTransaction();
                $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
                $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
                $newImage = ProductVariantImage::create([
                    'image_path' => $filePath,
                    'image_url' => url('/storage/' . $filePath),
                    'product_id' => $product->id,
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['error' => $e->getMessage()], 400);
        }
        return new ImageResource($newImage);
    }

    public function destroy(Product $product, ProductVariantImage $productVariantImage)
    {
        Storage::disk('public')->delete($productVariantImage->image_path);
        $productVariantImage->delete();
        return new ImageResource($productVariantImage);
    }

    public function order(Product $product, OrderImagesRequest $request)
    {
        $data = $request->validated();
        $orderArray = $data['order'];

        $productImages = $product->images;
        $productImagesCount = count($productImages);
        if ($productImagesCount !== count($orderArray)) {
            return Response::json(['error' => 'Invalid images count!'], 400);
        }

        foreach ($orderArray as $key=>$number) {
            foreach ($productImages as $productImage) {
                if ($productImage->id === $number) {
                    $productImage->position =$key;
                    $productImage->save();
                    continue 2;
                }
            }
        }
        return ImageResource::collection($productImages);
    }




    public function comparePage(Variant $variant)
    {
        $variantImages = $variant->images;
        $freeProductImagesAndVariantImages = ProductImage::where('product_id', $variant->product_id)->where('variant_id', '=', null)->orWhere('variant_id', $variant->id)->get();
        $images = $freeProductImagesAndVariantImages;

        return view('variant.photos', compact('variant', 'images', 'variantImages'));
    }

    public function compare(Request $request, Variant $variant)
    {

        if (!isset($request->images_ids)) {
            $freeProductImagesAndVariantImages = ProductImage::where('product_id', $variant->product_id)->where('variant_id', '=', null)->orWhere('variant_id', $variant->id)->get();
            foreach($freeProductImagesAndVariantImages as $image) {
                $image->variant_id = null;
                $image->save();
            }
        } else {
            $data = $request->validate([
                'images_ids' => 'required|array',
                'images_ids.*' => 'integer|required|exists:product_images,id',
            ]);

            $imagesIds = $data['images_ids'];

            $freeProductImagesAndVariantImages = ProductImage::where('product_id', $variant->product_id)->where('variant_id', '=', null)->orWhere('variant_id', $variant->id)->get();
            foreach($freeProductImagesAndVariantImages as $image) {
                $image->variant_id = null;
                $image->save();
            }

            foreach ($imagesIds as $imageId) {
                $image = ProductImage::find($imageId);
                $image['variant_id'] = $variant->id;
                $image->save();
            }
        }

        $product = $variant->product;
        $categories = Category::whereNull('parent_category_id')->get();
        return view('product.show', compact('product', 'categories'));
    }
}
