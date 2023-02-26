<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedVariant extends Model
{
    use HasFactory;
    protected $table = 'related_variants';
    protected $guarded = false;
}
