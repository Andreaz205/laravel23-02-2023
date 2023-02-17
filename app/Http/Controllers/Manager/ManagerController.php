<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
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
        $managers = Admin::all();
        return inertia('Manager/Index', [
            'managers' => $managers,
            'can-managers' => [
                'list' => Auth('admin')->user()->can('admin list'),
                'create' => Auth('admin')->user()->can('admin create'),
                'edit' => Auth('admin')->user()->can('admin edit'),
                'delete' => Auth('admin')->user()->can('admin delete'),
            ]
        ]);
    }

    public function edit(Admin $admin)
    {
        return inertia('Manager/Edit', [
            'manager' => $admin,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $newAdmin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return new AdminResource($newAdmin);
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->json(['status' => 'success']);
    }
}
