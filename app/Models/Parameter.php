<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parameter extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }
}
