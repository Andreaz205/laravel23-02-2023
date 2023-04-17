<?php

namespace App\Http\Requests\Variant\Content;

use Illuminate\Foundation\Http\FormRequest;

class AppendImageRequest extends FormRequest
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
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:20000'
        ];
    }
}
