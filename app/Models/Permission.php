<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as OriginalPermission;
class Permission extends OriginalPermission
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
    ];
}
