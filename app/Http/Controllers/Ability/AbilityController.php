<?php

namespace App\Http\Controllers\Ability;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ability\ToggleRolePermissionsRequest;
use App\Http\Services\Permission\PermissionService;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AbilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin list', ['only' => ['index', 'show']]);
        $this->middleware('can:admin create', ['only' => ['create', 'store']]);
        $this->middleware('can:admin edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:admin delete', ['only' => ['destroy']]);

    }

    public function index(PermissionService $PermissionService)
    {
        $roles = Role::with('permissions')->get();
//        $listPermissions = Permission::where('name', 'like', '%list%')->get();
//        foreach ($listPermissions as $permission) {
//            $permission->name = trim(explode('list', $permission->name)[0]);
//        }
        $sections = $PermissionService->sections();
        return inertia('Ability/Index', [
            'rolesData' => $roles,
            'sectionsData' => $sections,
            'can-admins' => [
                'list' => Auth('admin')->user()->can('admin list'),
                'create' => Auth('admin')->user()->can('admin create'),
                'edit' => Auth('admin')->user()->can('admin edit'),
                'delete' => Auth('admin')->user()->can('admin delete'),
            ]
        ]);
    }

    public function toggle(Role $role, Permission $permission, ToggleRolePermissionsRequest $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $section = explode(' ', $permission->name)[0];
        $newPermissionName = $section. " " . $type;
        $newPermission = Permission::where('name', $newPermissionName)->first();
        if ($role->hasPermissionTo($newPermission->name)) {
            $role->revokePermissionTo($newPermission->name);
            return Response::json(['status' => 'Deleted success!']);
        } else {
            $role->givePermissionTo($newPermission->name);
            return Response::json(['status' => 'Success']);
        }
    }
}
