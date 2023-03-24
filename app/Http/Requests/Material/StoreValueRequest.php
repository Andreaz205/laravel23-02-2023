<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class StoreValueRequest extends FormRequest
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
            'value' => 'required|string',
            'parent_material_unit_value_id' => 'nullable|integer|exists:material_unit_values,id'
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'Поле значение обязательное!',
            'value.string' => 'Поле значение должно быть строкой!',
            'parent_material_unit_value_id.integer' => 'Ошибка id - Должно быть числом!',
            'parent_material_unit_value_id.exists' => 'Ошибка parent id - Указанного вами значения не существует!',
        ];
    }
}
