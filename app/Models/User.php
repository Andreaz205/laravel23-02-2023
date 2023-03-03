<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function cart()
    {
        return $this->belongsToMany(Variant::class, 'users_cart_variants');
    }

    public function favorites()
    {
        return $this->belongsToMany(Variant::class, 'users_favorite_variants');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
        'kind',
        'jural_address',
        'inn',
        'phone',
        'additional_phone',
        'ogrn',
        'bic',
        'bank_name',
        'correspondent_account',
        'calculated_account',
        'unloading_address',
        'is_subscribed_to_news',
        'bonuses'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $url = url('password-reset?token=' . $token);
        $this->notify(new ResetPasswordNotification($url));
    }

    public function providers()
    {
        return $this->hasMany(Provider::class,'user_id','id');
    }
}
