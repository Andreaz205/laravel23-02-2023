<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Variant\VariantService;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderVariantCopy;
use App\Models\OrderVariants;
use App\Models\Variant;
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
        $user = Auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'payment_variant' => 'required|string|max:255',
            'delivery_type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'client_comment' => 'string|max:255',
            'variants' => 'required|array',
            'variants.*.id' => 'required|integer|exists:variants,id',
            'variants.*.quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }
        $data = $validator->validated();
        $orderedVariantsArray = collect($data['variants']);
        $orderedVariantsIds = $orderedVariantsArray->map(fn ($item) => $item['id']);

        if (isset($user)) $data['user_id'] = $user->id;
        unset($data['variants']);

        $sum = 0;
        $orderedVariants = Variant::whereIn('id', $orderedVariantsIds)->get();
        $orderedVariants->each(function  ($item) use(&$sum) {
            $sum = $sum + $item->price;
        });
        $data['sum'] = $sum;

        //check quantity
//        foreach($orderedVariantsArray as $arrayItem) {
//            $variantId = $arrayItem['id'];
//            $quantity = $arrayItem['quantity'];
//            $variant = Variant::findOrFail($variantId);
//            if ($quantity > $variant->quantity) {
//                return response()->json(['message' => 'quantity']);
//            }
//        }
        $copies = [];
        try {

            DB::beginTransaction();
            $newOrder = Order::query()->create($data);
            foreach ($orderedVariantsArray as $variantItem) {
                $variant = Variant::query()
                    ->where('id', $variantItem['id'])
                    ->with(['material_unit_values', 'images' => fn ($query) => $query->limit(1)])
                    ->first();
                $title = $variant->getTitleAttribute();
                $variantCopy = OrderVariantCopy::create([
                    'order_id' => $newOrder->id,
                    'product_link_id' => $variant->product_id,
                    'image_url' =>  isset($variant->images) && count($variant->images) > 0 ? $variant->images[0]->image_url : null,
                    'code' => $variant->code,
                    'price' => $variant->price,
                    'purchase_price' => $variant->purchase_price ?? 0,
                    'title' => $title,
                ]);
                $copies[] = $variantCopy;

                $quantity = $variantItem['quantity'];
                OrderVariants::create([
                    'order_id' => $newOrder->id,
                    'order_variant_copy_id' => $variantCopy->id,
                    'quantity' => $quantity
                ]);
            }

            OrderHistory::create([
                'order_id' => $newOrder->id,
                'note' => Auth('admin')->user()?->id ? 'Заказ создан пользователем '. Auth('admin')->user()?->name : 'Заказ создан',
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['status' => 'success', $copies]);
    }
}
