<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitProducts extends Model
{
    use HasFactory;
    protected $guarded = false;
    protected $withPivot = ['variant_id'];
}
