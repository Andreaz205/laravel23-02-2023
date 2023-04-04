<?php

namespace App\Http\Requests\MainPageSale;

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
            'sales' => 'required|array',
            'sales.*.id' => 'required|integer|exists:main_page_sales,id',
            'sales.*.description' => 'nullable|string|max:255',
            'sales.*.link' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'sales.required' => 'Не указано поле sales в запросе!',
            'sales.array' => 'sales должно быть массивом!',

            'sales.*.id.required' => 'Ошибка id',
            'sales.*.id.integer' => 'Id - должно быть числом!',
            'sales.*.id.exists' => 'Указанного id не существует!',

            'sales.*.description.string' => 'Описание должно быть строкой!',
            'sales.*.description.max' => 'Максимальная длина описания 255',


            'sales.*.link.string' => 'Ссылка должна быть строкой!',
            'sales.*.link.max' => 'Максимальная длина ссылки 255',
        ];
    }
}
