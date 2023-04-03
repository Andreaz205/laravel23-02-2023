<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Kit extends Model
{
    use HasFactory;
    use HasEagerLimit;
    protected $guarded = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, KitProducts::class, 'kit_id', 'product_id')->withPivot('variant_id');
    }
}
