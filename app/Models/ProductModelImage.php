<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductModelImage extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function product_model(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id', 'id');
    }
}
