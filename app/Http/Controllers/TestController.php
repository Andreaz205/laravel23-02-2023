<?php

namespace App\Http\Controllers;

use App\Http\Services\Variant\VariantService;
use App\Models\Variant;
use Illuminate\Http\Request;
require_once storage_path('/app/sms/sms.ru.php');

class TestController extends Controller
{

    public function index()
    {
        return inertia('Auth/Test');
    }

    public function putSomeDataToSession()
    {
        $smsru = new \SMSRU('E0DE3FC5-6582-0260-0052-8314A83A3396'); // Ваш уникальный программный ключ, который можно получить на главной странице
        $data = new \stdClass();
        $data->to = '89514790872';
        $data->text = 'Олег Монгол';

        // Текст сообщения
        // $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
        // $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
        // $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
        // $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
        // $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
        $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
        if ($sms->status == "OK") { // Запрос выполнен успешно
            return [
                "Сообщение отправлено успешно. ",
                "Сообщение отправлено успешно. ",
                "ID сообщения: $sms->sms_id. "
            ];
        } else {
            return ['сообщение не отправлено!'];
        }
    }

    public function getDataFromSession()
    {
        return  session('phone');
    }

    public function handleCaptcha()
    {

    }

    public function initToken(Request $request)
    {
        $token = $request->get('token');
        session()->put('recaptcha-token');
    }

    public function getRecaptchaToken()
    {
        return session('recaptcha-token');
    }

    public function testVariant(Variant $variant, VariantService $variantService)
    {
        $variantService->aggregateVariantByMaterialUnits($variant);
        return inertia('Test/Variant', [
            'data' => $variant
        ]);
    }
}

