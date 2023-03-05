<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionValue extends Model
{
    use HasFactory;

    protected $table = 'option_values';
    protected $guarded = false;
    protected $hidden = [
        'pivot'
    ];

    public function getColorLinkAttribute()
    {
        $variantsIds = OptionValueVariants::where('option_value_id', $this->id)->pluck('id')->toArray();
        return Variant::whereIn('id', $variantsIds)->get();
    }

    public function image()
    {
        return $this->hasOne(OptionImage::class, 'option_value_id', 'id');
    }
//    protected $with = ['option_name'];

//    public function option_name()
//    {
//        return $this->hasOne(OptionName::class, 'option_name_id', 'id');
//    }

}
