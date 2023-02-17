<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionName extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'option_names';
    protected $guarded = false;
    protected $with = ['default_option_value'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function option_values()
    {
        return $this->hasMany(OptionValue::class, 'option_name_id', 'id');
    }

    public function default_option_value()
    {
        return $this->hasOne(OptionValue::class, 'id', 'default_option_value_id');
    }
}
