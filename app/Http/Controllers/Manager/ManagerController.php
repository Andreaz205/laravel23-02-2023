<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Requests\Manager\UpdateRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Services\Permission\PermissionService;
use App\Models\Admin;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin list', ['only' => ['index', 'show']]);
        $this->middleware('can:admin create', ['only' => ['create', 'store']]);
        $this->middleware('can:admin edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:admin delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::all();
        $managers = Admin::all();
        return inertia('Manager/Index', [
            'managers' => $managers,
            'roles' => $roles,
            'user' =>  Auth('admin')->user(),
            'can-managers' => [
                'list' => Auth('admin')->user()->can('admin list'),
                'create' => Auth('admin')->user()->can('admin create'),
                'edit' => Auth('admin')->user()->can('admin edit'),
                'delete' => Auth('admin')->user()->can('admin delete'),
            ]
        ]);
    }

    public function edit(Admin $admin, PermissionService $PermissionService)
    {
        $roles = Role::all();

        $sections = $PermissionService->sections();
        return inertia('Manager/Edit', [
            'manager' => $admin->load('permissions', 'roles'),
            'sections' => $sections,
            'rolesData' => $roles
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        if (isset($data['role_id'])) {
            $roleId = $data['role_id'];
            $role = Role::find($roleId);
            unset($data['role_id']);
        }

        $newAdmin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (isset($role)) $newAdmin->assignRole($role);
        return new AdminResource($newAdmin);
    }

    public function update(Admin $admin, UpdateRequest $request)
    {
        $data = $request->validated();

        $sections = $data['sections'];

        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            $admin->roles()->detach();
            $admin->assignRole($role);
        }

        if (isset($data['name'])) $admin->update(['name' => $data['name']]);
        $admin->permissions()->detach();
        $permissions = [];
        foreach ($sections as $section) {
            if (isset($section['list_input']) && $section['list_input']) {
                $permissions[] = $section['name'].' '.'list';
            }
            if (isset($section['create_input']) && $section['create_input']) {
                $permissions[] = $section['name'].' '.'create';
            }
            if (isset($section['edit_input']) && $section['edit_input']) {
                $permissions[] = $section['name'].' '.'edit';
            }
            if (isset($section['delete_input']) && $section['delete_input']) {
                $permissions[] = $section['name'].' '.'delete';
            }
        }
        $admin->givePermissionTo($permissions);
        return $admin;
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->json(['status' => 'success']);
    }
}
