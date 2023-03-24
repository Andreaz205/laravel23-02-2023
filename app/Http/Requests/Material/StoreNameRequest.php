<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class StoreNameRequest extends FormRequest
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
            'title'=> 'required|string|unique:option_names,title',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Название обязательное!',
            'title.string' => 'Название должно быть строкой!',
            'title.unique' => 'Такое название уже существует!',
        ];
    }
}
