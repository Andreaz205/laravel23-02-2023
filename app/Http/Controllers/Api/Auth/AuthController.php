<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return Response::json(['error' => 'Incorrect email or password!'], 422);
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
        $user = User::create($data);
        if (isset($user)) {
          return $user->createToken('auth')->plainTextToken;
        } else {
            return response()->json([
                'error' => 'User creating failed!'
            ]);
        }
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
