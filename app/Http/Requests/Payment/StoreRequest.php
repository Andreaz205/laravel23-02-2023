<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    private string $regex = "/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/";
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
            'amount' => ['required', 'regex:'.$this->regex],
            'user_id' => 'nullable|integer|exists:users,id',
            'order_id' => 'required|integer|exists:orders,id',
            'description' => 'nullable|text|max:1000',
        ];
    }
}
