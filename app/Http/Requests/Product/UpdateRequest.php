<?php

namespace App\Http\Requests\Product;

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
            'width' => 'integer|nullable',
            'height' => 'integer|nullable',
            'length' => 'integer|nullable',
            'allow_sales' => 'boolean|nullable',
            'description' => 'string|max:255|nullable',
            'parameters' => 'array|nullable',
            'parameters.*.title' => 'required|string',
            'parameters.*.value' => 'required|string',
        ];
    }
}
