<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function prices()
    {
        return $this->hasMany(PriceVariants::class);
    }

    public function material_unit_values(): BelongsToMany
    {
        return $this->belongsToMany(MaterialUnitValue::class, MaterialUnitValueVariants::class);
    }

    public function option_values()
    {
        return $this->belongsToMany(OptionValue::class, OptionValueVariants::class, 'variant_id', 'option_value_id')->where('option_value_variants.deleted_at', null);
    }

    public function image()
    {
        return $this->hasMany(ProductVariantImage::class, 'variant_id', 'id')->limit(1);
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
        return Variant::whereIn('id', $relatedVariantsIds)->get();
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
        $materialValues = $this->material_unit_values()->get();
        $product = $this->product()->with(['materials' => fn ($query) => $query->with(['material_units'])])->first();
        $title = $product->title;

        $materialUnits = collect();
        foreach ($product['materials'] as $material) {
            $units = $material->material_units;
            $materialUnits->push(...$units);
        }

        if (isset($materialUnits) && count($materialUnits) > 0) {
            foreach ($materialUnits as $materialUnit) {
                if (isset($materialValues) && count($materialValues) > 0) {
                    foreach ($materialValues as $materialValue) {
                        if ($materialValue->material_unit_id === $materialUnit->id) {
                            $title = $title. ' '.$materialValue->value;
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
