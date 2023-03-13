<?php

namespace App\Http\Controllers\AccentProperty;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccentProperty\BindRequest;
use App\Http\Requests\AccentProperty\StoreRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\AccentProperty;
use App\Models\AccentPropertyMedia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AccentPropertyController extends Controller
{
    protected array $videoExtensions = ['avi', 'mp3', 'mp4'];
    public function all()
    {
        return AccentProperty::all();
    }

    public function productProperties()
    {

    }

    public function store(StoreRequest $request, UploadImageService $uploadImageService)
    {
        $data = $request->validated();
        $media = $data['file'];
        $media = $uploadImageService->upload($media);
        unset($data['file']);
        $name = $media['name'];
        $extension = $media['extension'];
        $path = $media['path'];
        $url = $media['url'];

        $accentProperty = AccentProperty::create($data);

        if (in_array($extension, $this->videoExtensions)) {
            AccentPropertyMedia::create([
                'video_url' => $url,
                'path' => $path,
                'accent_property_id' => $accentProperty->id,
            ]);
        } else {
            AccentPropertyMedia::create([
                'image_url' => $url,
                'path' => $path,
                'accent_property_id' => $accentProperty->id,
            ]);
        }
        return $accentProperty->load('media');
    }

    public function destroy(AccentProperty $accentProperty)
    {
        $accentProperty->delete();
        return Response::json(['status' => 'success']);
    }

    public function bind(Product $product, BindRequest $request)
    {
        $data = $request->validated();
        $product->accent_properties()->sync($data['accent_properties_ids']);
        $product->accent_properties = $product->accent_properties()->with('media')->get();
        return $product;
    }
}
