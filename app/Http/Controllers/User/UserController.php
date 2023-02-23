<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeUserKindRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private int $perPage = 20;

    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = User::with('group')->paginate($this->perPage);
        return inertia('User/Index', [
            'usersData' => $users,
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

    public function create()
    {
        return inertia('User/Create', [
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

    public function byTerm(Request $request)
    {
        $validator = Validator::make(['term' => $request->term], [
            'term' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validate();
        $term = $data['term'];

        $users = User::where('name', 'ILIKE', '%'.$term.'%')->paginate($this->perPage);
        return $users;
    }

    public function show(User $user)
    {
        $groups = Group::all();
        return inertia('User/Show', [
            'userData' => $user,
            'groupsData' => $groups,
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

    public function changeKind(User $user, ChangeUserKindRequest $request)
    {
        $data = $request->validated();
        $user->update($data);
        return new UserResource($user);
    }
}
