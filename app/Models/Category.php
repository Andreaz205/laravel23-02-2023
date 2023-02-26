<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $guarded = false;
//    protected $with = ['child_categories'];

//    public function parent_category()
//    {
//        return $this->belongsTo(Category::class, 'parent_category_id', 'id');
//    }

    public function child_categories()
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'id');
    }

    public function getProductVariantsAttribute()
    {
        $productIds = CategoryProducts::where('category_id', $this->id)->pluck('product_id')->toArray();
        return Variant::whereIn('product_id', $productIds)->get();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, CategoryProducts::class, 'category_id', 'product_id')->where('category_products.deleted_at', null);
    }

}
