<?php

namespace App\Http\Requests\Variant;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|integer|exists:materials,id',
            'materials.*.ids' => 'required|array',
            'materials.*.ids.*' => 'required|integer|exists:material_unit_values,id',
        ];
    }

    public function messages()
    {
        return [
            'materials.required' => 'Не указаны материалы!',
            'materials.array' => 'Указанные материалы должны быть представлены в виде массива!',
            'materials.*.material_id.required' => 'Не указан "material_id"!',
            'materials.*.material_id.integer' => '"material_id" - должен быть числом!',
            'materials.*.material_id.exists' => '"material_id" - указанного вами не существует!',
            'materials.*.ids.required' => 'Не указано поле ids для значений',
            'materials.*.ids.array' => 'Поле ids для значений должно быть массивом!',
            'materials.*.ids.*.required' => 'Массив ids для значений пуст!',
            'materials.*.ids.*.integer' => 'Указанные id для значений должны быть числом!',
            'materials.*.ids.*.exists' => 'Значений для указанных вами значений не существует!',
        ];
    }
}
