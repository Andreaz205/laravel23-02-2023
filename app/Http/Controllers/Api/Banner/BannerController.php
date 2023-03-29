<?php

namespace App\Http\Controllers\Api\Banner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Banner\BannerItemsResource;
use App\Models\BannerImages;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function mainPageData()
    {
        $bannerItems = BannerImages::orderBy('position', 'asc')->get();
        return BannerItemsResource::collection($bannerItems);
    }
}
