<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldRequest extends FormRequest
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
            'title' => 'required|string|unique:order_fields,title|max:50',
            'is_required' => 'required|boolean',
            'type' => 'required|in:string,text,bool,date',
            'is_user_fill' => 'required|boolean',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Название обязательно!',
            'title.string' => 'Название должно быть строкой!',
            'title.unique' => 'Название должно быть уникальным!',
            'title.max' => 'Название не должно длиннее 50 символов!',
            'is_required.required' => 'Поле "обязательное" должно быть заполнено!',
            'is_required.boolean' => 'Поле "обязательное" должно быть типа boolean!',
            'type.required' => 'Поле тип обязательное!',
            'is_required.in' => 'Значение поля тип должнол быть одним из "string, text, date, bool"!',
//            'is_user_fill.required' => 'Поле "заполняется клиентом" обязательное!',
//            'is_user_fill.boolean' => 'Поле "заполняется клиентом" должно быть типа boolean!',
            'description.string' => 'Поле "описание" должно быть строкой!',
            'description.max' => 'Поле "описание" должно быть не длиннее 255 символов!',
        ];
    }
}
