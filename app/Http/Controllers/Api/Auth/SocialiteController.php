<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{

    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
    }


    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $email = $user->getEmail();
        if (!isset($email))
            return redirect(env('FRONTEND_AUTH_CALLBACK').'?error=В указанном сервисе отсутсвет почта необходимая для регистрации! Попробуйте через другой сервис!');
        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
            [
                'avatar' => $user->getAvatar()
            ]
        );
        $token = $userCreated->createToken('auth')->plainTextToken;
        return redirect(env('FRONTEND_AUTH_CALLBACK').'?token='.$token.'&userId='.$userCreated->id);
//        return response()->json($userCreated . " " . $token, 200, ['Access-Token' => $token]);
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['vkontakte', 'yandex'])) {
            return response()->json(['error' => 'Please login using vkontakte'], 422);
        }
    }
}
