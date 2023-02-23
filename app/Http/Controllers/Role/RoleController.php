<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role list', ['only' => ['index', 'show']]);
        $this->middleware('can:role create', ['only' => ['create', 'store']]);
        $this->middleware('can:role edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:role delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::with('permissions')->all();

        return inertia('Ability/Role/Index', [
            'rolesData' => $roles,
            'can-roles' => [
                'list' => Auth('admin')->user()->can('role list'),
                'create' => Auth('admin')->user()->can('role create'),
                'edit' => Auth('admin')->user()->can('role edit'),
                'delete' => Auth('admin')->user()->can('role delete'),
            ]
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $role = Role::create([
            'name' => $data['role_name'],
            'guard_name' => 'admin'
        ]);
        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return Response::json(['status' => 'success']);
    }
}
