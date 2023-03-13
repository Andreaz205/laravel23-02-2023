<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    protected $guarded = false;
    protected $with = ['images'];

    public function images()
    {
        return $this->hasMany(ProductModelImage::class);
    }
}
