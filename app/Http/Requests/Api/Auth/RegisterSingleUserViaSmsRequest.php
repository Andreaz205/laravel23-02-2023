<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSingleUserViaSmsRequest extends FormRequest
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
            'phone' => 'required|string|min:11|max:11|unique:users,phone',
            'name' => 'required|string|max:255|min:2',
            'family' => 'required|string|max:255|min:2',
            'patronymic' => 'required|string|max:255|min:2',
        ];
    }
}
