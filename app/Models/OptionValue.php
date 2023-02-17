<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'option_values';
    protected $guarded = false;
//    protected $with = ['option_name'];

//    public function option_name()
//    {
//        return $this->hasOne(OptionName::class, 'option_name_id', 'id');
//    }

}
