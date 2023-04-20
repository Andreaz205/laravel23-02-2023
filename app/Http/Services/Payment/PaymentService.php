<?php

namespace App\Http\Services\Payment;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    private YooKassaService $yooKassaService;

    public function __construct(YooKassaService $yooKassaService)
    {
        $this->yooKassaService = $yooKassaService;
    }
    public function createPayment(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $order, ?User $user = null): string | null
    {
        $amount = $order->sum;
        $orderId = $order->id;
        try {
            DB::beginTransaction();

            $transaction = Transaction::query()->create([
                'amount' => $amount,
                'user_id' => $user?->id,
                'order_id' => $orderId
            ]);

            if ($transaction) {
                $link = $this->yooKassaService->createPayment($amount, [
                    'transaction_id' => $transaction->id
                ]);

                if (isset($link)) {
                    DB::commit();
                    return $link;
                }
                return null;
            }
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return null;
        }
    }
}
