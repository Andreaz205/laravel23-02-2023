<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderVariants;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:order list', ['only' => ['index', 'show']]);
        $this->middleware('can:order create', ['only' => ['create', 'store']]);
        $this->middleware('can:order edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:order delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return inertia('Order/Index', [
            'can-orders' => [
                'list' => Auth('admin')->user()?->can('order list'),
                'create' => Auth('admin')->user()?->can('order create'),
                'edit' => Auth('admin')->user()?->can('order edit'),
                'delete' => Auth('admin')->user()?->can('order delete'),
            ]
        ]);
    }

    public function data()
    {
        $orders = Order::orderBy('created_at', 'desc')->with('fields')->paginate(25);
        foreach ($orders as $order) {
            $sum = 0;
            $variants = $order->variants;
            foreach ($variants as $variant) {
                $notes = OrderVariants::where('order_id', $order->id)->where('order_variant_copy_id', $variant->id)->get();
                foreach ($notes as $note) {
                    if ($note->order_variant_copy_id === $variant->id && $note->order_id === $order->id) {
                        $sum += $note->quantity * $variant->price;
                    }
                }
            }
            $order->sum = $sum;
            $order->admin;
        }
        return $orders;
    }

    public function show(Order $order)
    {
        $variantsCopies = $order->variants()->get();
        foreach ($variantsCopies as $variantsCopy) {

            $variantsCopy->ordered_quantity = $variantsCopy->pivot->quantity;
        }

//        $orderVariantsNotes = OrderVariants::where('order_id', $order->id)->get(['order_variant_copy_id', 'quantity']);
//        foreach ($orderVariantsNotes as $orderVariantsNote) {
//            $variant = Variant::with('product')->findOrFail($orderVariantsNote->order_variant_copy_id);
//            $variant->ordered_quantity = $orderVariantsNote->quantity;
//            $variant->images =  $variant->images->last();
//            $variant->title = $variant->getTitleAttribute();
//            $variants[] = $variant;
//        }

        $order->history;
        $order->user = $order->user()->get();

        $admins = Admin::all();

        return inertia('Order/Show', [
            'orderData' => $order,
            'variantsData' => $variantsCopies,
            'adminsData' => $admins,
            'can-orders' => [
                'list' => Auth('admin')->user()?->can('order list'),
                'create' => Auth('admin')->user()?->can('order create'),
                'edit' => Auth('admin')->user()?->can('order edit'),
                'delete' => Auth('admin')->user()?->can('order delete'),
            ]
        ]);

//        return view('order.show', compact('variants', 'order', 'admins'));
    }

    public function changeStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        try {
            DB::beginTransaction();
            $message = 'Статус заказа изменён с \''. $order->getHumanStatusAttribute($order->status) .'\' на \''. $order->getHumanStatusAttribute($data['status']) .'\' пользователем '.Auth('admin')->user()->name;
            OrderHistory::create([
                'order_id' => $order->id,
                'note' => $message
            ]);
            $order->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
        $order->history;
        return $order;
    }

    public function togglePay(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'is_payed' => 'required|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        try {
            DB::beginTransaction();
            $message = 'Статус оплаты изменён с \''. $order->getPayStatusAttribute($order->is_payed) .'\' на \''. $order->getPayStatusAttribute($data['is_payed']) .'\' пользователем '.Auth('admin')->user()->name;

            OrderHistory::create([
                'order_id' => $order->id,
                'note' => $message
            ]);
            $order->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
        $order->history;
        return $order;
    }

    public function setPaymentVariant(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'payment_variant' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        try {
            DB::beginTransaction();
            $message = 'Способ оплаты изменён с \''. $order->getHumanPaymentVariantAttribute($order->payment_variant) .'\' на \''. $order->getHumanPaymentVariantAttribute($data['payment_variant']) .'\' пользователем '.Auth('admin')->user()->name;
            OrderHistory::create([
                'order_id' => $order->id,
                'note' => $message
            ]);
            $order->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
        $order->history;
        return $order;
    }

    public function setManager(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|integer'
        ]);
        if ($validator->fails()) {
            //Удаляем менеджера привязанного
            if ($request->admin_id === '-') {
                $message = 'Ответственный '. $order->admin?->name . ' удален пользователем ' . Auth('admin')->user()->name;
                try {
                    DB::beginTransaction();
                    OrderHistory::create([
                        'order_id' => $order->id,
                        'note' => $message
                    ]);
                    $order->update([
                        'duty_admin_id' => null
                    ]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['error' => $e->getMessage()], 422);
                }
                $order->history;
                return $order;
            }
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        try {
            DB::beginTransaction();
            $newAdmin = Admin::findOrFail($data['admin_id']);
            $message = 'Ответственный менеджер изменён с \''. $order->admin?->name .'\' на \''. $newAdmin->name .'\' пользователем '.Auth('admin')->user()->name;
            OrderHistory::create([
                'order_id' => $order->id,
                'note' => $message
            ]);
            $order->update([
                'duty_admin_id' => $data['admin_id']
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
        $order->history;
        return $order;
    }

    public function setAdminComment(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'admin_comment' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        try {
            DB::beginTransaction();
            $message = 'Комментарий продавца изменён пользователем '.Auth('admin')->user()->name;
            OrderHistory::create([
                'order_id' => $order->id,
                'note' => $message
            ]);
            $order->update([
                'admin_comment' => $data['admin_comment']
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
        $order->history;
        return $order;
    }
}
