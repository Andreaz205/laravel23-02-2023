<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FavoriteController extends Controller
{
    public function getFavoriteVariants(Request $request)
    {
        $user = $request->user();
        return  $user->favorites;
    }

    public function toggle(Variant $variant, Request $request)
    {
        $user = $request->user();
        $user->favorites()->toggle($variant->id);
        return $user->favorites;
    }

    public function clear(Request $request)
    {
        $user = $request->user();
        $user->favorites()->sync([]);
        return Response::json(['status' => 'success']);
    }
}
