<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class AppendSizeRequest extends FormRequest
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
            'length' => 'required|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'title' => 'required|string|min:2',
        ];
    }
}
