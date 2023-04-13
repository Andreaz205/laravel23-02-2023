<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailCode extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode();
        }
        parent::__construct($attributes);
    }

    public function generateCode($codeLength = 6)
    {
        $min = pow(10, $codeLength - 1);
        $max = $min * 10 - 1;
        return mt_rand($min, $max);
    }
}
