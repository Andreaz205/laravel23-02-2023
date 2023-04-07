<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResetsPasswords;


    protected string $broker = 'users';

    public function login(Request $request)
    {
        if (auth('sanctum')->user()) {
            return response()->json([
                'message' => 'User is already authorized'
            ], 403);
        }
        $data = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:100', 'confirmed'],
        ]);

        if (Auth::guard('web')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return Auth('web')->user()->createToken('auth')->plainTextToken;
        } else {
            return Response::json(['error' => 'Неправильный логин или пароль!'], 422);
        }
    }

    public function logout()
    {
        if (!auth('sanctum')->user()) {
            return response()->json([
                'message' => 'Your are not authorized!'
            ], 403);
        }
        auth('sanctum')->user()->currentAccessToken()->delete();
        return Response::json(['status' => 'success']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'max:100', 'confirmed'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create($data);
            $newToken = $user->createToken('auth')->plainTextToken;
            $defaultGroup = Group::query()->where('is_default', true)->first();
            if (isset($defaultGroup)) $user->update (['group_id' => $defaultGroup->id]);

            $userFields = UserField::whereUserKind('single')->get();
            $map = [];
            foreach ($userFields as $userField) {
                $map[] = [
                    'user_id' => $user->id,
                    'user_field_id' => $userField->id,
                    'value' => null,
                ];
            }
            UserFieldUsers::query()->insert($map);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => $exception->getMessage()], 500);
        }
        return $newToken;
    }

    /**
     * @throws ValidationException
     */
    public function sendForgotPasswordLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $status = Password::broker('users')->sendResetLink($data);

        if ($status == Password::RESET_LINK_SENT) {
            return new JsonResponse(['status' => trans($status)], 200);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
