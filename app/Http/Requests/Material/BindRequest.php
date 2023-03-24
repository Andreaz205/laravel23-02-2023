<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class BindRequest extends FormRequest
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
            'materials' => 'nullable|array',
            'materials.*' => 'required|integer|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'materials.array' => 'Свойства в запросе должны быть массивом',
            'materials.*.required' => 'Массив свойств пустой!',
            'materials.*.integer' => 'Свойства в массиве должны быть целочисленными',
            'materials.*.exists' => 'Указзаных id для свойств не существует!',
        ];
    }
}
