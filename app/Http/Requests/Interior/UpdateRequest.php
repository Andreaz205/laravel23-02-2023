<?php

namespace App\Http\Requests\Interior;

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
            'points' => 'required|array|max:5',
            'points.*.left' => 'required|numeric|min:0|max:100',
            'points.*.top' => 'required|numeric|min:0|max:100',
            'points.*.variant_id' => 'required|integer|exists:variants,id',
            'points.*.description' => 'nullable|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'points.required' => 'Укажите точки',
            'points.array' => 'Указнные точки должны быть в формате массива',
            'points.max' => 'Максимальное количество точек 5',

            'points.*.left.required' => 'Укажите значение отступа слева!',
            'points.*.left.numeric' => 'Значение отступа слева должно быть типа numeric',
            'points.*.left.min' => 'Минимальное значение слева 0',
            'points.*.left.max' => 'Максимальное значение слева 100',

            'points.*.top.required' => 'Отступ сверху обязателен!',
            'points.*.top.numeric' => 'Значение отступа сверху должно быть типа numeric',
            'points.*.top.min' => 'Минимальное значение сверху 0',
            'points.*.top.max' => 'Максимальное значение сверху 100',

            'points.*.variant_id.required' => 'Не указан вариант',
            'points.*.variant_id.integer' => 'Указанный id для варианта должне быть числом',
            'points.*.variant_id.exists' => 'Указанного варианта не существует!',

            'points.*.description.string' => 'Описание должно быть строкой',
            'points.*.description.max' => 'Длина описания не должна превышать 255 символов!'
        ];
    }
}
