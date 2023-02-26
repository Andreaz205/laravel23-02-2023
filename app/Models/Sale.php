<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function getProductsAttribute($sale)
    {
        $productIds = ProductSales::where('sale_id', $sale->id)->pluck('product_id')->toArray();
        return Product::whereIn('id', $productIds)->get();
    }
}

