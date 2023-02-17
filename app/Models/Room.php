<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public function categories()
    {
        return $this->belongsToMany(Category::class, RoomCategories::class, 'room_id','category_id')->whereNull('room_categories.deleted_at');
    }
}
