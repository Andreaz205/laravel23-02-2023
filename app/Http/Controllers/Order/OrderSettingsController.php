<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderField;
use Illuminate\Http\Request;

class OrderSettingsController extends Controller
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
        $fields = OrderField::all();
        return inertia('Order/Settings/Index', [
            'fields' => $fields,
            'can-orders' => [
                'list' => Auth('admin')->user()?->can('order list'),
                'create' => Auth('admin')->user()?->can('order create'),
                'edit' => Auth('admin')->user()?->can('order edit'),
                'delete' => Auth('admin')->user()?->can('order delete'),
            ]
        ]);
    }
}
