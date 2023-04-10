<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class UserPhoneCode extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function sendCode(): bool
    {
        if (! $this->code) {
            $this->code = $this->generateCode();
        }
        try {

            //  Здесь нужно вставить логику для отправки сообщений!!!


            Log::info("Your verification code - {$this->code}, your number - {$this->phone_number}");



        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

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
