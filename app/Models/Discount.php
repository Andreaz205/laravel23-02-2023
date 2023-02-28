<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_discounts');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'discount_groups');
    }
}
