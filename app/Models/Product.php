<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = false;

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
        return $this->belongsToMany(Category::class, CategoryProducts::class, 'product_id', 'category_id');
    }
}
