<?php

namespace App\Http\Controllers\Product\DeliverySettings;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class YandexController extends Controller
{
    public function index(Product $product)
    {
        return inertia('Product/Delivery/Yandex/Index', [
            'product' => $product
        ]);
    }
}
