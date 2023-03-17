<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreFieldRequest;
use App\Models\Order;
use App\Models\OrderField;
use App\Models\OrderFieldOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class OrderFieldController extends Controller
{
    public function store(StoreFieldRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $field = OrderField::create($data);
            $users = Order::all();
            $result = [];
            foreach ($users as $user) {
                $result[] = [
                    'order_id' => $user->id,
                    'order_field_id' => $field->id,
                    'value' => '',
                ];
            }
            OrderFieldOrder::insert($result);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $field;
    }

    public function destroy(OrderField $field)
    {
        $field->delete();
        return 111;
    }
}
