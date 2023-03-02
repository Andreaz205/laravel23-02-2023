<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bonus extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'bonus_categories');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'bonus_groups');
    }
}
