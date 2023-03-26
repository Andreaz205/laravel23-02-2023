<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaterialUnitValue extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function material_unit(): BelongsTo
    {
        return $this->belongsTo(MaterialUnit::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'id', 'material_unit');
    }

    public function child_values(): HasMany
    {
        return $this->hasMany(MaterialUnitValue::class, 'parent_material_unit_value_id', 'id');
    }

    public function color(): HasOne
    {
        return $this->hasOne(Color::class, 'last_material_unit_value_id', 'id');
    }

}
