<?php

namespace App\Http\Requests\Banner;

use Illuminate\Foundation\Http\FormRequest;

class AttachLinkRequest extends FormRequest
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
            'link' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'link.required' => 'Ссылка обязательная!',
            'link.string' => 'Ссылка должна быть строкой!',
        ];
    }
}
