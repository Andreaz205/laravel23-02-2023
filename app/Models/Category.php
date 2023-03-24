<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        return $this->hasMany(Product::class);
    }

    public function option_names()
    {
        return $this->belongsToMany(OptionName::class, CategoryOptionNames::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, CategoryMaterials::class);
    }

}
