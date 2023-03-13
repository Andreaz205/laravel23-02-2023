<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserField;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    public function index()
    {
        $fields = UserField::all();
        return inertia('User/UserSettings/UserSettings', [
            'fieldsProps' => $fields,
            'can-users' => [
                'list' => Auth('admin')->user()?->can('user list'),
                'create' => Auth('admin')->user()?->can('user create'),
                'edit' => Auth('admin')->user()?->can('user edit'),
                'delete' => Auth('admin')->user()?->can('user delete'),
            ]
        ]);
    }

}
