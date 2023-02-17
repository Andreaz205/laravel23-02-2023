<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Services\Order\OrderService;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(OrderService $service)
    {
        $this->orderService = $service;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'payment_variant' => 'required|string|max:255',
            'delivery_type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'client_comment' => 'string|max:255',
            'variants' => 'required|array',
            'variants.*.id' => 'required|integer',
            'variants.*.quantity' => 'required|integer',
            'variants.*.price' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }
        $data = $validator->validated();
        $orderedVariantsArray = $data['variants'];
        unset($data['variants']);
        //check quantity
//        foreach($orderedVariantsArray as $arrayItem) {
//            $variantId = $arrayItem['id'];
//            $quantity = $arrayItem['quantity'];
//            $variant = Variant::findOrFail($variantId);
//            if ($quantity > $variant->quantity) {
//                return response()->json(['message' => 'quantity']);
//            }
//        }
        $status = $this->orderService->checkPrice($orderedVariantsArray);
        if ($status === 'fail') {
            return response()->json(['message' => 'Incorrect price'], 400);
        }
        try {
            DB::beginTransaction();
            $newOrder = Order::create($data);
            foreach($orderedVariantsArray as $arrayItem) {
                $variantId = $arrayItem['id'];
                $quantity = $arrayItem['quantity'];
                OrderVariants::create([
                    'order_id' => $newOrder->id,
                    'variant_id' => $variantId,
                    'quantity' => $quantity
                ]);
            }
            OrderHistory::create([
                'order_id' => $newOrder->id,
                'note' => Auth('admin')->user()?->id ? 'Заказ создан пользователем '.Auth('admin')->user()?->name : 'Заказ создан',
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success']);
    }
}
