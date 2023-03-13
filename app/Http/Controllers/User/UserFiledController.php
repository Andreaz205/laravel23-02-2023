<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreFieldRequest;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class UserFiledController extends Controller
{
    public function store(StoreFieldRequest $request)
    {
        $data = $request->validated();
        try {
          DB::beginTransaction();

            $field = UserField::create($data);
            $users = User::all();
            $result = [];

           foreach ($users as $user) {
               $result[] = [
                   'user_id' => $user->id,
                   'user_field_id' => $field->id,
                   'value' => '',
               ];
           }
            UserFieldUsers::insert($result);
          DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => $exception->getMessage()], 500);
        }
        return $field;
    }

    public function destroy(UserField $field)
    {
        $field->delete();
        return Response::json(['status' => 'success']);
    }
}
