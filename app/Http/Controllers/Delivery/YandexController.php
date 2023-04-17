<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\Yandex\UpdateRequest;
use App\Http\Services\Delivery\YandexDeliveryService;
use App\Models\YandexDeliveryData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YandexController extends Controller
{
    private YandexDeliveryService $service;

    public function __construct(YandexDeliveryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = YandexDeliveryData::query()->first();

        $body = [
            'available_for_dropoff' => true,
//            'type' => 'terminal'
        ];
        $pvz = $this->service->pvzList($body);
        return inertia('Delivery/Yandex/Yandex', [
            'data' => $data,
            'points' => $pvz,
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
