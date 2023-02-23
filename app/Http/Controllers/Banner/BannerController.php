<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\OrderBannerItemsRequest;
use App\Http\Requests\Banner\StoreBannerItemsRequest;
use App\Models\BannerImages;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:banner list', ['only' => ['index', 'show']]);
        $this->middleware('can:banner create', ['only' => ['create', 'store']]);
        $this->middleware('can:banner edit', ['only' => ['edit', 'update', 'order']]);
        $this->middleware('can:banner delete', ['only' => ['destroy']]);
    }

    public function store(StoreBannerItemsRequest $request)
    {
        $data = $request->validated();
        $file = $data['item'];
        $availableExtensions = ['avi', 'mp4', 'jpg', 'jpeg', 'png', 'webp', 'gif'];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $fileExtension = $file->getClientOriginalExtension();
        if (!in_array($fileExtension, $availableExtensions)) return Response::json(['error' => "Unavailable extension!"], 400);
        try {
            DB::beginTransaction();
            $name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $filePath = Storage::disk('public')->putFileAs('images', $file, $name);
            $existsBannerImagesPositions = BannerImages::pluck('position')->toArray();
            $maxPosition = 0;
            foreach ($existsBannerImagesPositions as $existsBannerImagesPosition) {
                if ($existsBannerImagesPosition > $maxPosition) {
                    $maxPosition =  $existsBannerImagesPosition;
                }
            }

            // Поиск максимальной позиции чтобы влепить новый элемент на 1 больше
            if (in_array($fileExtension, $imageExtensions)) {
                $newBannerItem = BannerImages::create([
                    'image_url' => url('/storage/images/' . $name),
                    'file_path' => $filePath,
                    'position' => $maxPosition + 1
                ]);
            } else {
                $newBannerItem = BannerImages::create([
                    'video_url' => url('/storage/images/' . $name),
                    'file_path' => $filePath,
                    'position' => $maxPosition + 1
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['error' => $e->getMessage()], 400);
        }
        return $newBannerItem;
    }

    public function order(OrderBannerItemsRequest $request)
    {
        $data = $request->validated();
        $orderArray = $data['order'];
        $bannerItems = BannerImages::all();
        $bannerItemsLength = count($bannerItems);
        if ($bannerItemsLength !== count($orderArray)) return Response::json(['error' => 'Requested items count does not match records count!'], 400);
        foreach ($orderArray as $key=>$orderItemId) {
            $item = $this->findById($orderItemId, $bannerItems);
            $item->update(['position' => $key]);
        }
        return $bannerItems;
    }

    public function destroy(BannerImages $item)
    {
        $item->delete();
        return Response::json(['status' => 'Deleted successfully!']);
    }

    protected function  findById($id, $items)
    {
        foreach ($items as $item) {
            if ($item->id === $id) return $item;
        }
        return null;
    }
}
