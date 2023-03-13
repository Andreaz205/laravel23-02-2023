<?php

namespace App\Http\Services\Image;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class UploadImageService
{
    public function upload($image, $disk = 'public', $path = 'images', $extensions = null)
    {
//        if (isset($extensions)) {
//            $imageExtension = $image->getClientOriginalExtension();
//            if (!in_array($imageExtension, $extensions)) throw Va
//        }
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
        $url = url('/storage/' . $filePath);
        return [
            'name' => $name,
            'path' => $filePath,
            'url' => $url,
            'extension' => $image->getClientOriginalExtension()
        ];
    }
}
