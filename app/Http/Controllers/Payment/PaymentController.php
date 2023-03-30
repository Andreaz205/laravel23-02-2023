<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\YooKassaService;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;
use function PHPUnit\Framework\throwException;

class PaymentController extends Controller
{
    public function index()
    {
//        $transactions = Transaction::orderByDesc('created_at')->get();
//        return inertia('Payment/Index', ['transactions' => $transactions]);
    }

    public function store(Request $request, YooKassaService $yooKassaService)
    {
        $amount = (float)$request->input('amount');
        $description = (string)$request->input('description');

        try {
          DB::beginTransaction();
            $transaction = Transaction::create([
                'amount' => $amount,
                'description' => $description,
            ]);

            if ($transaction) {
                $link = $yooKassaService->createPayment($amount, $description, [
                    'transaction_id' => $transaction->id
                ]);

                if (isset($link)) {
                    DB::commit();
                    return $link;
                }
                throw ValidationException::withMessages(['message' => 'some_error']);
            }
            throw ValidationException::withMessages(['message' => 'some_error']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => $exception->getMessage()], 500);
        }
    }

    public function callback(YooKassaService $yooKassaService)
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        Log::info($requestBody);
        try {
            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($requestBody)
                : new NotificationWaitingForCapture($requestBody);
            $payment = $notification->getObject();
            if (isset($payment->status) && $payment->status === 'waiting_for_capture') {
                $client = $yooKassaService->getClient();
                $client->capturePayment([
                    'amount' => $payment->amount,
                ], $payment->id, uniqid('', true));
            }
            if (isset($payment->status) && $payment->status === 'canceled') {
                $metadata = (object)$payment->metadata;
                if(isset($metadata->transaction_id)) {
                    $transactionId = (int)$metadata->transaction_id;
                    $transaction = Transaction::find($transactionId);
                    $transaction->update(['status' => PaymentStatusEnum::FAILED]);
                }
            }
            if (isset($payment->status) && $payment->status === 'succeeded') {
                if ((bool)$payment->paid === true) {
                    $metadata = (object)$payment->metadata;
                    if(isset($metadata->transaction_id)) {
                        $transactionId = (int)$metadata->transaction_id;
                        $transaction = Transaction::find($transactionId);
                        $transaction->update(['status' => PaymentStatusEnum::CONFIRMED]);
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }
}
