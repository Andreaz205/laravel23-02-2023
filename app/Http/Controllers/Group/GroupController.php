<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreRequest;
use App\Http\Requests\Group\UpdateRequest;
use App\Http\Resources\Group\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:group list', ['only' => ['index', 'show']]);
        $this->middleware('can:group create', ['only' => ['create', 'store']]);
        $this->middleware('can:group edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:group delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $groups = Group::all();
        return inertia('Group/Index', [
            'groupsData' => $groups,
            'can-groups' => [
                'list' => Auth('admin')->user()?->can('group list'),
                'create' => Auth('admin')->user()?->can('group create'),
                'edit' => Auth('admin')->user()?->can('group edit'),
                'delete' => Auth('admin')->user()?->can('group delete'),
            ]
        ]);
    }

    public function create(StoreRequest $request)
    {
        $data = $request->validated();
        $title = $data['title'];
        $group = Group::where('title', $title)->first();
        if (isset($group) && count($group) > 0) {
            return response()->json(['error' => 'Value already exists!'], 422);
        }
        return Group::create(['title' => $title]);
    }

    public function show(Group $group)
    {
        return inertia('Group/Show', [
            'groupData' => $group,
            'can-groups' => [
                'list' => Auth('admin')->user()?->can('group list'),
                'create' => Auth('admin')->user()?->can('group create'),
                'edit' => Auth('admin')->user()?->can('group edit'),
                'delete' => Auth('admin')->user()?->can('group delete'),
            ]
        ]);
    }

    public function update(UpdateRequest $request, Group $group)
    {
        $data = $request->validated();
        $group->update($data);
        return new GroupResource($group);
    }
}
