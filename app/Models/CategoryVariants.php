<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryVariants extends Model
{

    use SoftDeletes;
    protected $table = 'category_variants';
    protected $guarded = false;
}
