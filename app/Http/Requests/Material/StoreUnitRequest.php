<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'parent_material_unit_id' => 'nullable|integer|exists:material_units,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Название элемента обязательное!',
            'title.string' => 'Название элемента должно быть строкой!',
            'title.max' => 'Название элемента не длиннее 255 символов!',
            'parent_material_unit_id.integer' => 'Ошибка id - integer - Перезагрузите страницу!',
            'parent_material_unit_id.exists' => 'Ошибка parent id - Перезагрузите страницу!',
        ];
    }
}
