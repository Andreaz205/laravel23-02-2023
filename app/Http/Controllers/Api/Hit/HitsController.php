<?php

namespace App\Http\Controllers\Api\Hit;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Hit\HitsResource;
use App\Models\Category;
use App\Models\CategoryProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HitsController extends Controller
{
    public function data()
    {
        $hitCategory = Category::where('name', "Лидеры продаж")->first();
        if (!isset($hitCategory)) return Response::json(['error' => 'Hit Category does not exists!'], 500);
        $hitProducts = $hitCategory->products;
        return HitsResource::collection($hitProducts);
    }
}
