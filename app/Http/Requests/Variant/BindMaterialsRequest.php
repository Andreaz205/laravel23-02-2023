<?php

namespace App\Http\Requests\Variant;

use Illuminate\Foundation\Http\FormRequest;

class BindMaterialsRequest extends FormRequest
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
            'material_id' => 'required|integer|exists:materials,id',
            'material_unit_values' => 'required|array',
            'material_unit_values.*' => 'required|integer|exists:material_unit_values,id',
        ];
    }

    public function messages()
    {
        return [
            'material_id.required' => 'Не указано поле "material_id"!',
            'material_id.integer' => 'Поле "material_id" должно быть типа integer!',
            'material_id.exists' => 'Указанного вами "material_id" не существует!',

            'material_unit_values.required' => 'Не указано поле "material_unit_values"!',
            'material_unit_values.array' => 'Поле "material_unit_values" должно быть массивом!',

            'material_unit_values.*.required' => 'Не указаны элементы массива "material_unit_values"!',
            'material_unit_values.*.integer' => 'Элементы массива "material_unit_values" должны быть числом типа integer!',
            'material_unit_values.*.exists' => 'Указанных id для material_unit_values не существует!',
        ];
    }
}
