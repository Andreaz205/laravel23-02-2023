<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'jural_address' => 'string|nullable',
            'inn' => 'string|nullable',
            'phone' => 'required|string',
            'additional_phone' => 'string|nullable',
            'ogrn' => 'string|nullable',
            'bic' => 'string|nullable',
            'bank_name' => '',
            'correspondent_account' => 'string|nullable',
            'calculated_account' => 'string|nullable',
            'unloading_address' => 'string|nullable',
            'is_subscribed_to_news' => 'boolean',
            'group_id' => 'nullable|integer|exists:groups,id'
        ];
    }
}
