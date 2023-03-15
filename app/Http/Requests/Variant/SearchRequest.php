<?php

namespace App\Http\Requests\Variant;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'term' => 'required|string|max:255',
            'count' => 'nullable|integer|max:50',
        ];
    }

    public function messages()
    {
        return [
            'term.required' => 'Введите термин по которому будет поиск!',
            'term.string' => 'Введённый термин должен быть строкой!',
            'term.max' => 'Максимальная длина термина не должна превышать 255 символов!',
            'count.integer' => 'Количество страниц должно быть числом!!',
            'count.max' => 'Максимальное количество страниц не более 50!'
        ];
    }
}
