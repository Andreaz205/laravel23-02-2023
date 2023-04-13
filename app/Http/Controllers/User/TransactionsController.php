<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(User $user)
    {
        $transactions = $user->transactions;
        return inertia('User/UserTransactions', [
            'transactions' => $transactions,
            'user' => $user,
        ]);
    }
}
