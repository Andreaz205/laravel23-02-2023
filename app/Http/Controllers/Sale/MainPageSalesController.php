<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainPageSale\UpdateRequest;
use App\Http\Requests\Sale\MainPageSaleStoreImageRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\MainPageSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class MainPageSalesController extends Controller
{
    public function index()
    {
        $sales = MainPageSale::limit(4)->orderByDesc('created_at')->get();
        return inertia('Sale/Sale',[
            'sales' => $sales,
        ]);
    }

    public function loadImage(MainPageSaleStoreImageRequest $request, MainPageSale $sale, UploadImageService $uploadImageService)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $data = $uploadImageService->upload($data['image']);
            $sale->update([
                'image_url' => $data['url'],
                'image_path' => $data['path'],
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $sale;
    }

    public function update(UpdateRequest $request)
    {
        $salesToUpdate = $request->validated()['sales'];

        try {
            DB::beginTransaction();
            foreach ($salesToUpdate as $saleToUpdate) {
                $sale = MainPageSale::find($saleToUpdate['id']);
                $sale->update([
                   'description' => $saleToUpdate['description'],
                   'link' => $saleToUpdate['link'],
                ]);
            }
            DB::commit();
        } catch (\Exception $exception){
            DB::rollBack();
            return redirect('/admin/main-page-sales')->with('message', $exception->getMessage());
        }
        return redirect('/admin/main-page-sales')->with('message', 'Акции успешно обновлены!');
    }
}
