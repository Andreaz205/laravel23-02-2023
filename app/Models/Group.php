<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function discounted_categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_groups');
    }
}
