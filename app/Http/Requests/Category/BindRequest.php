<?php

namespace App\Http\Requests\Category;

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
            'category_id' => 'required|integer|exists:categories,id'
        ];
    }

    public function messages()
    {
        return [
          'category_id.required' => 'Необходимо указать id для связывания',
          'category_id.integer' => 'Id должен быть целочисленным!',
          'category_id.exists' => 'Указанной вами категориии не существует!',
        ];
    }
}
