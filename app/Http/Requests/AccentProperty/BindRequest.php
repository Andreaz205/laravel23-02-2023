<?php

namespace App\Http\Requests\AccentProperty;

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
            'accent_properties_ids' => 'nullable|array',
            'accent_properties_ids.*' => 'required|integer|exists:accent_properties,id',
        ];
    }

    public function messages()
    {
        return [
            'accent_properties_ids.array' => 'Указанные id должны быть массивом, обновите страницу',
            'accent_properties_ids.*.required' => 'Указанные id обязтаельно должны быть в массиве, обновите страницу',
            'accent_properties_ids.*.integer' => 'Указанные id обязтаельно должны быть в массиве числом, обновите страницу',
            'accent_properties_ids.*.exists:accent_properties,id' => 'Указанные id обязтаельно должны быть в базе, обновите страницу',
        ];
    }
}
