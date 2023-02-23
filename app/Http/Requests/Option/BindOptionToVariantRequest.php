<?php

namespace App\Http\Requests\Option;

use Illuminate\Foundation\Http\FormRequest;

class BindOptionToVariantRequest extends FormRequest
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
            'option_name_id' => 'required|integer',
            'option_value_id' => 'required|integer',
        ];
    }
}
