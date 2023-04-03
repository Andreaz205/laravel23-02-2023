<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUnitValueVariants extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
