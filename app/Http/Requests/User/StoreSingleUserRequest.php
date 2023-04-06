<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreSingleUserRequest extends FormRequest
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
            'phone' => 'required|string',
            'is_subscribed_to_news' => 'boolean',
            'group_id' => 'nullable|integer|exists:groups,id',
            'fields' => 'nullable|array',
            'fields.*.id' => 'required|exists:user_fields,id|integer',
            'fields.*.value' => 'nullable|max:255',
        ];
    }

    public function messages()
    {
        return [
            'fields.array' => 'Поле "fields" должно быть массивом!',
            'fields.*.id.required' => 'В массиве "fields" необходимо указать id для field',
            'fields.*.id.integer' => 'В массиве "fields" id должен быть типа integer',
            'fields.*.id.exists' => 'В массиве "fields" указанный id отсутствует в базе',
            'fields.*.value.max' => 'В массиве "fields" value не должен превышать 255 символов!',
            'fields.*.value.string' => 'В массиве "fields" value должен быть типа string!',
        ];
    }
}
