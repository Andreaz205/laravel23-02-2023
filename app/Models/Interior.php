<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Interior extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function image(): HasOne
    {
        return $this->hasOne(InteriorImage::class, 'interior_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
