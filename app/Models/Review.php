<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }

    public function answer()
    {
        return $this->hasOne(ReviewAnswer::class, 'review_id', 'id');
    }
}
