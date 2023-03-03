<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function prices()
    {
        return $this->hasMany(PriceVariants::class);
    }

    public function option_values()
    {
        return $this->belongsToMany(OptionValue::class, OptionValueVariants::class, 'variant_id', 'option_value_id')->where('option_value_variants.deleted_at', null);
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class, 'variant_id', 'id');
    }

    public function getOptionValuesAttribute()
    {
        $optionValueIds = OptionValueVariants::where('variant_id', $this->id)->pluck('option_value_id')->toArray();
        return OptionValue::whereIn('id', $optionValueIds)->get();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function variantCategories()
    {
        return $this->belongsToMany(Category::class, 'category_variants')->where('category_variants.deleted_at', null);
    }

    public function getRelatedVariantsAttribute()
    {
        $relatedVariantsIds = RelatedVariant::where('parent_variant_id', $this->id)->pluck('related_variant_id')->toArray();
        return  Variant::whereIn('id', $relatedVariantsIds)->get();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'variant_id', 'id')->orderBy('id', 'desc');
    }

    public function getPublishedReviewsAttribute()
    {
        return $this->hasMany(Review::class, 'variant_id', 'id')->where('published', true)->orderBy('id', 'desc')->get(['id', 'content', 'mark', 'name', 'created_at']);
    }

    public function richTemplates()
    {
        return $this->hasMany(RichContent::class, 'variant_id', 'id');
    }

    public function getTitleAttribute()
    {
        $optionValuesIds = OptionValueVariants::where('variant_id', $this->id)->pluck('option_value_id')->toArray();
        $optionValues = OptionValue::whereIn('id', $optionValuesIds)->get(['id', 'title', 'option_name_id']);
        $title = '';
        $optionNames = OptionName::where('product_id', $this->product->id)->get(['id']);
        if (isset($optionNames) && count($optionNames) > 0) {
            foreach ($optionNames as $optionName) {
                if (isset($optionValues) && count($optionValues) > 0) {
                    foreach ($optionValues as $optionValue) {
                        if ($optionValue->option_name_id === $optionName->id) {
                            $title = $title. ' '.$optionValue->title;
                        }
                    }
                }
            }
        }
        return $title;
    }

    public function rich_content()
    {
        return $this->hasOne(RichContent::class, 'variant_id', 'id');
    }
}
