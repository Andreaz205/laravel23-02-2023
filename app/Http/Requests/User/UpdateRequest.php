<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

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
            'is_subscribed_to_news' => 'boolean|nullable',
            'name' => 'string|required',
            'email' => 'email|required|unique:users,email,'. request()->user->id,
            'phone' => 'string|nullable',
            'jural_address' => 'string|nullable',
            'additional_phone' => 'string|nullable',
            'ogrn' => 'string|nullable',
            'bic' => 'string|nullable',
            'inn' => 'string|nullable',
            'bank_name' => 'string|nullable',
            'correspondent_account' => 'string|nullable',
            'calculated_account' => 'string|nullable',
            'unloading_address' => 'string|nullable',
            'group_id' => 'nullable|integer|exists:groups,id',
            'fields' => 'nullable|array',
            'fields.*.user_field_id' => 'required|integer|exists:user_field_users,id',
            'fields.*.value' => 'nullable',
        ];
    }
}
