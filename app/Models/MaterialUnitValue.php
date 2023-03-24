<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialUnitValue extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function material_unit(): BelongsTo
    {
        return $this->belongsTo(MaterialUnit::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id', 'material_unit');
    }

}
