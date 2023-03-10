<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryProducts extends Model
{
    use SoftDeletes;
    protected $table = 'category_products';
    protected $guarded = false;
}
