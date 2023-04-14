<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessLinesController extends Controller
{
    public function index()
    {
        return inertia('Delivery/BusinessLines');
    }
}
