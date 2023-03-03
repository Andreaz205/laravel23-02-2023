<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class CartController extends Controller
{


    public function getCartVariants(Request $request)
    {

        $user = $request->user();
        return  $user->cart;
    }

    public function toggle(Variant $variant, Request $request)
    {
        $user = $request->user();
        $user->cart()->toggle($variant->id);
        return $user->cart;
    }

    public function clear(Request $request)
    {
        $user = $request->user();
        $user->cart()->sync([]);
        return Response::json(['status' => 'success']);
    }
}
