<?php

namespace App\Http\Requests\Statistic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatisticRequest extends FormRequest
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
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'detailing' => Rule::in(['day', 'week', 'month'])
        ];
    }

    public function messages()
    {
        return [
            'form.date' => 'Поле from должно быть датой',
            'to.date' => 'Поле to должно быть датой',
            'detailing' => 'Поле detailing должно быть одниз из day', 'week', 'month',
        ];
    }
}
