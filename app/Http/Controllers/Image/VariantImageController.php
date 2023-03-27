<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\StoreImageRequest;
use App\Http\Requests\VariantImage\BindImagesVariantRequest;
use App\Http\Resources\Image\ImageResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Services\Image\UploadImageService;
use App\Models\Product;
use App\Models\ProductVariantImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class VariantImageController extends Controller
{
    public function bind(Product $product, Variant $variant, BindImagesVariantRequest $request)
    {
        $data = $request->validated();
        $imagesIds = $data['images_ids'];
        $images = ProductVariantImage::whereIn('id', $imagesIds)->get();
        $variantImages = $variant->images;
        // TODO: Поверить чтобы принадлежали продукту
        //Я так заебался эту хуйню писать здесь столько ошибок было!!!!
        try {
            DB::beginTransaction();
            foreach ($images as $image) {
                $image->variant_id = $variant->id;
                $image->save();
            }
            foreach ($variantImages as $variantImage) {
                if (!in_array($variantImage->id, $imagesIds)) {
                    $variantImage->variant_id = null;
                    $variantImage->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['errors' => [$e->getMessage()]], 422);
        }
//        $refreshedProductImages = $product->images;
//        $refreshedVariants = $product->variants->with('images');
//        $product->variants->load('images');
//        $product->load('images');

//        return response()->json(['data' => $product]);
//        return ImageResource::collection($refreshedProductImages);
//        return Response::json(['status' => 'success']);
        return 1111;
    }

    public function storeImage(StoreImageRequest $request, Product $product, Variant $variant, UploadImageService $uploadImageService)
    {
        $data = $request->validated();
        $image = $data['image'];

        try {
            DB::beginTransaction();

            $data = $uploadImageService->upload($image);
            $imageUrl = $data['url'];
            $filePath = $data['path'];

            $newImage = ProductVariantImage::create([
                'image_path' => $filePath,
                'image_url' => $imageUrl,
                'variant_id' => $variant->id,
                'product_id' => $product->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['error' => $e->getMessage()], 400);
        }
        return new ImageResource($newImage);
    }
}
