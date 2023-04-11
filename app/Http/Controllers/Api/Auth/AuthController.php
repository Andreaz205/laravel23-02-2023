<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ConfirmLoginViaSmsRequest;
use App\Http\Requests\Api\Auth\ConfirmRegisterSingleUserViaSms;
use App\Http\Requests\Api\Auth\LoginViaSmsRequest;
use App\Http\Requests\Api\Auth\RegisterSingleUserViaSmsRequest;
use App\Http\Services\User\UserService;
use App\Models\User;
use App\Models\UserPhoneCode;
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

    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function registerOrganization(Request $request)
    {
//        $data = $request->validate([
//            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
//            'password' => ['required', 'string', 'max:100', 'confirmed'],
//            'name' => ['required', 'string', 'max:100'],
//        ]);
//
//        try {
//            DB::beginTransaction();
//
//            $user = User::create($data);
//
//            $newToken = $user->createToken('auth')->plainTextToken;
//
//            $this->service->handleAppendDefaultGroup($user);
//            $this->service->appendFieldsAfterCreating($user);
//
//            DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            return Response::json(['message' => $exception->getMessage()], 500);
//        }
//        return $newToken;
    }

    public function login(Request $request)
    {
//        if (auth('sanctum')->user()) {
//            return response()->json([
//                'message' => 'User is already authorized'
//            ], 403);
//        }
//        $data = $request->validate([
//            'email' => ['required', 'email', 'max:100'],
//            'password' => ['required', 'string', 'max:100', 'confirmed'],
//        ]);
//        if (Auth::guard('web')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
//            return Auth('web')->user()->createToken('auth')->plainTextToken;
//        } else {
//            return Response::json(['error' => 'Неправильный логин или пароль!'], 422);
//        }
    }

    public function logout()
    {
        if (!auth('sanctum')->user()) {
            return response()->json([
                'message' => 'Вы не авторизованы!'
            ], 403);
        }
        auth('sanctum')->user()->currentAccessToken()->delete();
        return Response::json(['status' => 'success']);
    }

    /**
     * @throws ValidationException
     */
    public function sendForgotPasswordLink(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//           'email' => 'required|email|exists:users,email',
//        ]);
//        if ($validator->fails()) {
//            return Response::json(['errors' => $validator->errors()], 422);
//        }
//
//        $data = $validator->validated();
//
//        $status = Password::broker('users')->sendResetLink($data);
//
//        if ($status == Password::RESET_LINK_SENT) {
//            return new JsonResponse(['status' => trans($status)], 200);
//        }
//
//        throw ValidationException::withMessages([
//            'email' => [trans($status)],
//        ]);
    }





    // Sms для физических лиц!
    /**
     * @throws ValidationException
     */
    public function registerSingleUserViaSms(RegisterSingleUserViaSmsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $candidate = User::where('phone', $data['phone'])->first();
        if (isset($candidate))
            throw ValidationException::withMessages(['Пользователь с таким телефоном уже существует!']);
        $code = UserPhoneCode::query()->create(['phone_number' => $data['phone']]);
        $isSent = $code->sendCode();
        if ($isSent) {
            return Response::json(['message' => 'На указанный номер был выслан код подтверждения!']);
        } else {
            return Response::json(['message' => 'Ошибка, мы не смогли выслать код!'], 500);
        }
    }

    /**
     * @throws ValidationException
     */
    public function confirmRegisterSingleUserViaSms(ConfirmRegisterSingleUserViaSms $request)
    {
        $data = $request->validated();
        $queryCode = (int)$data['code'];
        try {
            DB::beginTransaction();

            if ($this->service->checkCode($data['phone'], $queryCode)) {
               $user = User::query()->create($data);
               $this->service->handleAppendDefaultGroup($user);
               $this->service->appendFieldsAfterCreating($user);
               $token = $user->createToken('auth')->plainTextToken;

            } else {
                throw ValidationException::withMessages(['Указанного номера телефона нет либо код недействителен!']);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => 'Что-то пошло не так!'], 500);
        }
        return Response::json(['user' => $user, 'token' => $token]);
    }

    /**
     * @throws ValidationException
     */
    public function loginViaSms(LoginViaSmsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $phone = $data['phone'];
        $user = User::query()->where('phone', $phone)->first();
        if (isset($user)) {
            $code = UserPhoneCode::query()->create(['phone_number' => $phone]);
            $isSent = $code->sendCode();
            if ($isSent) {
                return Response::json(['message' => 'На указанный номер был выслан код подтверждения!']);
            } else {
                return Response::json(['message' => 'Ошибка, мы не смогли выслать код!'], 500);
            }
        }
        throw ValidationException::withMessages(['Пользователя с таким номером не существует!']);
    }

    public function confirmLoginViaSms(ConfirmLoginViaSmsRequest $request)
    {
        $data = $request->validated();
        $phone = $data['phone'];
        $queryCode = (int)$data['code'];
        try {
            DB::beginTransaction();

            if ($this->service->checkCode($phone, $queryCode)) {
                $user = User::query()->where('phone', $phone)->first();
                $token = $user->createToken('auth')->plainTextToken;
            } else {
                throw ValidationException::withMessages(['Указанного номера телефона нет либо код недействителен!']);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => 'Что-то пошло не так!'], 500);
        }
        return Response::json(['user' => $user, 'token' => $token]);
    }



}

