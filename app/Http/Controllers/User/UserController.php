<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeUserKindRequest;
use App\Http\Requests\User\StoreOrganizationRequest;
use App\Http\Requests\User\StoreSingleUserRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Group;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $groups = Group::all();
        $fields = UserField::all();

        return inertia('User/Create', [
            'groups' => $groups,
            'fields' => $fields,
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

    public function storeOrganisation(StoreOrganizationRequest $request)
    {
        $data = $request->validated();
        $password = $data['password'];
        $hashedPassword = Hash::make($password);
        $data['password'] = $hashedPassword;
        $data['kind'] = 'organization';
//        if (!isset($data['group_id'])) {
//            $defaultGroup = Group::query()->where('is_default', true)->first();
//            if (isset($defaultGroup)) $data['group_id'] = $defaultGroup->id;
//        }
        try {
            DB::beginTransaction();
            $user = User::create($data);
            $fields = UserField::all();
            $map = [];
            foreach ($fields as $field) {
                $map[] = [
                    'user_field_id' => $field->id,
                    'user_id' => $user->id,
                    'value' => '',
                ];
            }
            User::insert($map);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['message' => $error->getMessage()], 500);
        }
        return redirect('/admin/users/'. $user->id)->with('message', 'Пользователь успешно создан');
    }

    public function storeSingleUser(StoreSingleUserRequest $request)
    {
        $data = $request->validated();
        dd($data);
        $password = $data['password'];
        $hashedPassword = Hash::make($password);
        $data['password'] = $hashedPassword;
        $data['kind'] = 'single';
//        if (!isset($data['group_id'])) {
//            $defaultGroup = Group::query()->where('is_default', true)->first();
//            if (isset($defaultGroup)) $data['group_id'] = $defaultGroup->id;
//        }
        try {
            DB::beginTransaction();
            $user = User::create($data);
            $fields = UserField::all();
            $map = [];
            foreach ($fields as $field) {
                $map[] = [
                    'user_field_id' => $field->id,
                    'user_id' => $user->id,
                    'value' => '',
                ];
            }
            User::insert($map);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['message' => $error->getMessage()], 500);
        }

        return redirect('/admin/users/'. $user->id)->with('message', 'Пользователь успешно создан');
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
        $fields = UserField::where('user_kind', $user->kind)->get();
        $user->fields = $user->fields()->get()->map(function ($field) use($fields, $user) {
            foreach ($fields as $globalUserField) {
                if ($field->user_field_id === $globalUserField->id) {
                    if ($globalUserField->user_kind === $user->kind) {
                        return $field;
                    }
                }
            }
            return null;
        })->filter()->all();
        return inertia('User/Show', [
            'userData' => $user,
            'groupsData' => $groups,
            'fields' => $fields,
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

    public function update(User $user, UpdateRequest $request)
    {
        $data = $request->validated();
        if (isset($data['fields'])) {
            foreach ($data['fields'] as $field) {
                $searchedField = UserFieldUsers::find($field['user_field_id']);
                $searchedField->update(['value' => $field['value']]);
            }
        }

        $user->update($data);
        return new UserResource($user);
    }

    public function changeKind(User $user, ChangeUserKindRequest $request)
    {
        $data = $request->validated();
        $user->update(['kind' => $data['kind']]);
        $map = [];
        $globalUserFields = UserField::where('user_kind', $user->kind)->get();
        $user->fields = $user->fields()->get()->map(function ($field) use($globalUserFields, $user) {
            foreach ($globalUserFields as $globalUserField) {
                if ($field->user_field_id === $globalUserField->id) {
                    if ($globalUserField->user_kind === $user->kind) {
                        return $field;
                    }
                }
            }
            return null;
        })->filter()->all();
        $fields = UserField::where('user_kind', $user->kind)->get();
        return Response::json(['data' => [
            "user" => $user,
            'fields' => $fields
        ]]);
//        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect('/admin/users')->with('message', 'Пользователь' . $user->name .' успешно удалён');
    }
}
