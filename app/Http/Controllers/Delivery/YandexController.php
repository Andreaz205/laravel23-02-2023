<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\Yandex\UpdateRequest;
use App\Models\YandexDeliveryData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YandexController extends Controller
{
    public function index()
    {
        $data = YandexDeliveryData::query()->first();
        return inertia('Delivery/Yandex', [
            'data' => $data
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            YandexDeliveryData::query()->delete();
            YandexDeliveryData::query()->create($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            return redirect()->back()->with('message', $message);
        }
        return redirect('/admin/delivery/yandex')->with('message', 'Данные успешно обновлены!');
    }

}
