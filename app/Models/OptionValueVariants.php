<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionValueVariants extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'option_value_variants';
    protected $guarded = false;
}
