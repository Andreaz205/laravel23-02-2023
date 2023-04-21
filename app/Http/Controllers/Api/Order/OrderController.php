<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Payment\PaymentService;
use App\Http\Services\Variant\VariantService;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderVariantCopy;
use App\Models\OrderVariants;
use App\Models\Variant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(OrderService $service)
    {
        $this->orderService = $service;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function store(Request $request, OrderService $orderService, PaymentService $paymentService)
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
            'variants.*.id' => 'required|integer|exists:variants,id',
            'variants.*.quantity' => 'required|integer',
            'tinkoff_application_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();

        $user = Auth('sanctum')->user();
        $userClass = get_class($user);
        if ($userClass === 'App\Models\Admin') $user = null;
        if (isset($user)) $data['user_id'] = $user->id;
        unset($data['variants']);

        if ($data['payment_variant'] === 'installment_tinkoff') {
            if (! isset($data['tinkoff_application_id'])) {
                throw ValidationException::withMessages(['Не указан id завяки на рассрочку!']);
            } else {
                $tinkoffApplicationId = $data['tinkoff_application_id'];
                if ($isExists = Order::query()->where('tinkoff_application_id', $tinkoffApplicationId)->exists())
                    throw ValidationException::withMessages(['Заказ с таким же номером заявки на рассрочку Tinkoff уже существует!']);;
            }
        }

        $orderedVariantsArray = collect($data['variants']);
        $orderedVariantsIds = $orderedVariantsArray->map(fn ($item) => $item['id']);

        $bonuses = $orderService->calculateBonuses($orderedVariantsIds, $user);
        $data['bonuses'] = $bonuses;

        try {
            DB::beginTransaction();

            $variants = Variant::query()
                ->whereIn('id', $orderedVariantsIds)
                ->with(['material_unit_values', 'images' => fn ($query) => $query->limit(1)])
                ->get();
            $orderService->attachUserPriceByGroup($variants, $user);
            $sumData = $this->orderService->getOrderSumFromRequestWithPreparedPrices($orderedVariantsArray, $variants);
            $data['sum'] = $sumData['sum'];
            $data['purchase_sum'] = $sumData['purchase_sum'];

            $newOrder = Order::query()->create($data);

            foreach ($orderedVariantsArray as $variantItem) {
                $variant = $variants->find($variantItem['id']);

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

                $quantity = $variantItem['quantity'];
                OrderVariants::create([
                    'order_id' => $newOrder->id,
                    'order_variant_copy_id' => $variantCopy->id,
                    'quantity' => $quantity
                ]);
            }

            OrderHistory::create([
                'order_id' => $newOrder->id,
                'note' => $user ? 'Заказ создан пользователем '. $user?->name : 'Заказ создан',
            ]);
            if ($data['payment_variant'] === 'card') {
                $link = $paymentService->createPayment($newOrder, $user);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json([
            'order_status' => 'success',
            'payment_link' => $link ?? null,
        ]);
    }
}
