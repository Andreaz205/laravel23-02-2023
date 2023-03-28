<?php

namespace App\Http\Controllers\Interior;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interior\StoreImageRequest;
use App\Http\Requests\Interior\StoreRequest;
use App\Http\Requests\Interior\UpdateRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\Interior;
use App\Models\InteriorImage;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class InteriorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:interior list', ['only' => ['index', 'show']]);
        $this->middleware('can:interior create', ['only' => ['create', 'store']]);
        $this->middleware('can:interior edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:interior delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $interiors = Interior::with(['image', 'variants' => function ($query) {
            $query->with('images');
        }])->get();

        foreach ($interiors as $interior) {
            if (isset($interior->variants)) {
                foreach ($interior->variants as $variant) {
                    $variant->title = $variant->getTitleAttribute();
                }
//                $interior->variant->title = $interior->variant->getTitleAttribute();
            }
        }
//        foreach ($interiors as $interior) {
//            if (isset($interior->variant)) {
//                $interior->variant->title = $interior->variant->getTitleAttribute();
//                $interior->variant->load(['images' => fn ($query) => $query->limit(1)]);
//            }
//        }
        return inertia('Interior/Index', [
            'interiors' => $interiors,
            'can-groups' => [
                'list' => Auth('admin')->user()?->can('interior list'),
                'create' => Auth('admin')->user()?->can('interior create'),
                'edit' => Auth('admin')->user()?->can('interior edit'),
                'delete' => Auth('admin')->user()?->can('interior delete'),
            ]
        ]);
    }

    public function storeImage(StoreImageRequest $request, Interior $interior, UploadImageService $imageUploadService)
    {
        $data = $request->validated();

        $data = $imageUploadService->upload($data['image']);
        $image = $interior->image()->create([
            'image_url' => $data['url'],
            'image_path' => $data['path'],
        ]);
        return $image;
    }

    public function store(StoreRequest $request, Interior $interior, UploadImageService $uploadImageService)
    {
        $data = $request->validated();
        $points = $data['points'];
        try {
            DB::beginTransaction();

            $existsImage = $interior->image;
            if (isset($existsImage)) {
                $existsImage->delete();
                Storage::disk('public')->delete($existsImage->path);
            }

            $imageData = $uploadImageService->upload($data['image']);
            InteriorImage::create([
               'image_url' => $imageData['url'],
               'image_path' => $imageData['path'],
               'interior_id' => $interior->id,
            ]);

            $result = [];
            foreach ($points as $point) {
                $variantId = $point['variant_id'];
                $left = $point['top'];
                $top = $point['left'];
                $result[$variantId] = ['left' => $left, 'top' => $top];
            }
            $interior->variants()->sync($result);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => [$exception->getMessage()]], 422);
        }
        $interior->load(['image', 'variants' => fn ($query) => $query->with('images')]);
        if (isset($interior->variants)) {
            foreach ($interior->variants as $variant) {
                $variant->title = $variant->getTitleAttribute();
            }
        }
        return $interior;
    }

    public function update(Interior $interior, UpdateRequest $request)
    {
        $data = $request->validated();
        $points = $data['points'];
        try {
            DB::beginTransaction();
            $result = [];
            foreach ($points as $point) {
                $variantId = $point['variant_id'];
                $left = $point['top'];
                $top = $point['left'];
                $result[$variantId] = ['left' => $left, 'top' => $top];
            }
            $interior->variants()->sync($result);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => [$exception->getMessage()]], 422);
        }
        $interior->load(['image', 'variants' => fn ($query) => $query->with('images')]);
        if (isset($interior->variants)) {
            foreach ($interior->variants as $variant) {
                $variant->title = $variant->getTitleAttribute();
            }
        }
        return $interior;
    }

    public function appendVariant(Interior $interior, Variant $variant)
    {
        $interior->update(['variant_id' => $variant->id]);
        $title = $variant->getTitleAttribute();
        $variant->title = $title;
        return $variant->load(['images' => fn ($query) => $query->limit(1)]);
    }

    public function deleteVariant(Interior $interior, Variant $variant)
    {
        $interior->update(['variant_id' => null]);
        return $variant;
    }

    public function deleteImage(Interior $interior)
    {
        $interior->image()->delete();
        return 111;
    }


    public function destroy(Interior $interior)
    {
        $interior->image()->delete();
        $interior->variants()->sync([]);
        return 111;
    }
}
