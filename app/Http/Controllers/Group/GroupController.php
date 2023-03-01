<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreRequest;
use App\Http\Requests\Group\UpdateRequest;
use App\Http\Resources\Group\GroupResource;
use App\Http\Services\Category\CategoryService;
use App\Http\Services\Group\GroupService;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GroupController extends Controller
{
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
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

    public function show(Group $group, CategoryService $categoryService)
    {
        $categories = $this->groupService->categoriesWithCheckedProp($group);
        $categories = $categoryService->nestedCategories($categories);
        return inertia('Group/Show', [
            'groupData' => new GroupResource($group),
            'categoriesData' => $categories,
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
        if (isset($data['categories'])) {
            $categories = $data['categories'];
            unset($data['categories']);
        }
        if (isset($data['is_default']) && $data['is_default']) {
            $mustBeDefault = true;
        }
        try {
            DB::beginTransaction();
            if (isset($mustBeDefault)) {
                $existsDefaultGroups = Group::query()
                    ->where('is_default', true)
                    ->whereNot('id', $group->id)
                    ->get();
                if (count($existsDefaultGroups) > 0) {
                    foreach ($existsDefaultGroups as $existsDefaultGroup) {
                        $existsDefaultGroup->update(['is_default' => false]);
                    }
                }
            }
            $group->update($data);
            if (isset($categories)) $group->discounted_categories()->sync($categories);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error->getMessage()]);
        }

        return new GroupResource($group);
    }
}
