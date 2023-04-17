<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantContent extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function material_unit_values(): BelongsToMany
    {
        return $this->belongsToMany(MaterialUnitValue::class, MaterialUnitValueVariantContent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VariantContentItem::class);
    }

    public function text_items(): HasMany
    {
        return $this->hasMany(VariantContentTextItem::class);
    }
}
