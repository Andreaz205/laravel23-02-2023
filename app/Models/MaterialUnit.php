<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaterialUnit extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function values(): HasMany
    {
        return $this->hasMany(MaterialUnitValue::class);
    }

    public function child_unit(): HasOne
    {
        return $this->hasOne(MaterialUnit::class, 'parent_material_unit_id', 'id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MaterialUnit::class, 'parent_material_unit_id', 'id');
    }
}
