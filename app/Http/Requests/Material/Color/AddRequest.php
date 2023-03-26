<?php

namespace App\Http\Requests\Material\Color;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'image' => 'required|file|mimes:jpg,png,webp,gif,jpeg|max:10000',
            'material_unit_value_id' => 'required|integer|exists:material_unit_values,id'
        ];
    }

    public function messages()
    {
        return [
            'material_unit_value_id.required' => 'Не указано поле "material_unit_value_id"',
            'material_unit_value_id.integer' => 'Поле "material_unit_value_id" должно быть числом',
            'material_unit_value_id.exists' => 'Указанного "material_unit_value_id" не существует!',
            'image.required' => 'Укажите изображение!',
            'image.file' => 'Изображение должно быть файлом!',
            'image.mimes' => 'Изображение должно быть файлом следующих форматов: jpg,png,webp,gif,jpeg!',
            'image.max' => 'Максимальный размер файла 10000 кб!',
        ];
    }
}
