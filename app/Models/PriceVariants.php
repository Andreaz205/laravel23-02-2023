<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceVariants extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
}
