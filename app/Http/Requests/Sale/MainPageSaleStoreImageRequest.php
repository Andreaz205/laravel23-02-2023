<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class MainPageSaleStoreImageRequest extends FormRequest
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
            'image' => 'required|file|max:10000|mimes:jpg,gif,jpeg,png,webp'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Изображение обязательно!',
            'image.file' => 'Изображение обязательно!',
            'image.max' => 'Размер изображения слишком большой!',
            'image.mimes' => 'Недопустимый формат!',
        ];
    }
}
