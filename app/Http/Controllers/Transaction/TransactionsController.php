<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Services\Payment\YooKassaService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionsController extends Controller
{
    public function index()
    {

        $perPage = 20;
        $transactions = Transaction::query()->with(['user', 'order'])->latest()->paginate($perPage);

        return inertia('Transaction/Index', [
            'transactions' => $transactions
        ]);
    }
}
