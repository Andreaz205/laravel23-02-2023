<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function fields()
    {
        return $this->hasMany(OrderFieldOrder::class, );
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, OrderVariants::class, 'order_id', 'variant_id');
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id')->orderBy('created_at', 'desc');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'duty_admin_id');
    }

    public function getHumanStatusAttribute($status)
    {
        if ($status === 'new') {
            return 'Новый';
        }
        if ($status === 'preparing') {
            return 'В обработке';
        }
        if ($status === 'agreed') {
            return 'Согласован';
        }
        if ($status === 'stored') {
            return 'Отгружен';
        }
        if ($status === 'delivered') {
            return 'Доставлен';
        }
        if ($status === 'aborted') {
            return 'Отменён';
        }
        if ($status === 'client_enabled') {
            return 'Покупатель не доступен';
        }
        if ($status === 'back') {
            return 'Возврат';
        }
        if ($status === 'back_process') {
            return 'В процессе возврата';
        }

        return null;
    }

    public function getPayStatusAttribute($isPayed)
    {
        if ($isPayed === true) {
            return 'Оплачен';
        } else {
            return 'Не оплачен';
        }
    }

    public function getHumanPaymentVariantAttribute($variant)
    {
        if ($variant === 'cash') {
            return 'Наличные';
        }
        if ($variant === 'card') {
            return 'Онлайн оплата картой';
        }
        if ($variant === 'partials') {
            return 'Оплата по частям';
        }
        if ($variant === 'out_variant') {
            return 'Внешний способ';
        }
        return null;
    }
}
