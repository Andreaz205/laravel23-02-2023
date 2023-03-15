<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderSettingsController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('can:order list', ['only' => ['index', 'show']]);
//        $this->middleware('can:order create', ['only' => ['create', 'store']]);
//        $this->middleware('can:order edit', ['only' => ['edit', 'update']]);
//        $this->middleware('can:order delete', ['only' => ['destroy']]);
//    }

    public function index()
    {
        return inertia('Order/Settings/Index');
    }
}
