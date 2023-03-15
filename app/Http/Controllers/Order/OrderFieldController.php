<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreFieldRequest;
use Illuminate\Http\Request;

class OrderFieldController extends Controller
{
    public function store(StoreFieldRequest $request)
    {
        $data = $request->validated();
        dd($data);
    }
}
