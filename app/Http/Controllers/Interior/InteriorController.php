<?php

namespace App\Http\Controllers\Interior;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interior\StoreImageRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\Interior;
use App\Models\InteriorImage;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

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
        $interiors = Interior::with(['image', 'variant' => function ($query) {
            $query->with('images');
        }])->get();

        foreach ($interiors as $interior) {
            if (isset($interior->variant)) {
                $interior->variant->title = $interior->variant->getTitleAttribute();
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
}
