<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccentProperty extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function media()
    {
        return $this->hasOne(AccentPropertyMedia::class);
    }
}
