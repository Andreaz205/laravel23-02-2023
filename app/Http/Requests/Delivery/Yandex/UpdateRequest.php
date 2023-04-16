<?php

namespace App\Http\Requests\Delivery\Yandex;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'payment_method' => ['required', Rule::in(['already_paid', 'card_on_receipt', 'cash_on_receipt', 'cashless'])],
            'platform_id' => 'required|string|max:255',
            'platform_address' => 'required|string|max:255',
        ];
    }
}
