<?php

namespace App\Http\Services\Permission;

use App\Models\Permission;

class PermissionService
{
    public function sections()
    {
        $listPermissions = Permission::where('name', 'like', '%list%')->get();
        foreach ($listPermissions as $permission) {
            $permission->name = trim(explode('list', $permission->name)[0]);
        }
        return $listPermissions;
    }
}
