<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as OriginalPermission;
class Permission extends OriginalPermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
    ];
}
