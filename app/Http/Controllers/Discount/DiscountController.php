<?php

namespace App\Http\Controllers\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:room list', ['only' => ['index', 'show']]);
        $this->middleware('can:room create', ['only' => ['create', 'store']]);
        $this->middleware('can:room edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:room delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return inertia('Discount/Index', [
            'can-discount' => [
                'list' => Auth('admin')->user()?->can('discount list'),
                'create' => Auth('admin')->user()?->can('discount create'),
                'edit' => Auth('admin')->user()?->can('discount edit'),
                'delete' => Auth('admin')->user()?->can('discount delete'),
            ]
        ]);
    }
}
