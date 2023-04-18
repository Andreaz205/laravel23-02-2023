<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\BusinessLines\UpdateRequest;
use App\Models\BusinessLinesDeliveryData;
use App\Models\YandexDeliveryData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessLinesController extends Controller
{
    public function index()
    {
        $data = BusinessLinesDeliveryData::query()->first();
        return inertia('Delivery/BusinessLines', [
            'data' => $data
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            BusinessLinesDeliveryData::query()->delete();
            BusinessLinesDeliveryData::query()->create($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            return redirect()->back()->with('message', $message);
        }
        return redirect('/admin/delivery/business-lines')->with('message', 'Данные успешно обновлены!');
    }

}
