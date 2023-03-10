<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = false;

//    public function variants_count()
//    {
//        return $this->hasMany(Variant::class, 'product_id', 'id');
//    }


    public function options()
    {
        return $this->hasMany(Option::class, 'id', 'product_id');
    }

    public function option_names()
    {
        return $this->hasMany(OptionName::class, 'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class, 'product_id', 'id');
    }

    public function option_values()
    {
        return $this->hasMany(OptionValue::class, 'product_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, CategoryProducts::class, 'product_id', 'category_id')->where('category_products.deleted_at', null);
    }


    public function getMinPriceAttribute()
    {
        $variants = $this->variants;
        if (count($variants)> 0) {
            $minPrice = $variants[0]->price;
            foreach($variants as $variant) {
                if ($variant->price < $minPrice) $minPrice = $variant->price;
            }
            return $minPrice;
        }
        return 0;
    }

    public function getQuantityAttribute()
    {
        $variants = $this->variants;
        $quantity = 0;
        foreach ($variants as $variant) {
            $quantity += $variant->quantity;
        }
        return $quantity;
    }

    public function getMinMaxPriceAttribute()
    {
        $variants = $this->variants;
        if (count($variants)> 0) {
            $minPrice = $maxPrice = $variants[0]->price;
            foreach($variants as $variant) {
                if ($variant->price < $minPrice) $minPrice = $variant->price;
                if ($variant->price > $maxPrice) $maxPrice = $variant->price;
            }
            return ['min_price' => $minPrice, 'max_price' => $maxPrice];
        }
        return ['min_price' => 0, 'max_price' => 0];
    }

    public function getColorsAttribute()
    {
        $optionNames = $this->option_names;
        $colorsOptionName = null;
        foreach ($optionNames as $optionName) {
            if ($optionName->is_color) {
                $colorsOptionName = $optionName;
                break;
            }
        }
        return $colorsOptionName?->option_values;
    }
}
