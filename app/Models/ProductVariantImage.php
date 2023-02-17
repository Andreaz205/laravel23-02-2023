<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductVariantImage extends Model
{
    use SoftDeletes;
    protected $guarded = false;

    public function getUrlAttribute()
    {
        return url('storage/' . $this->path);
    }

    public static function clearStorage()
    {
        $images = ProductVariantImage::whereNull('product_id')->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
