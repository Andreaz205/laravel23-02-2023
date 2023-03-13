<?php

namespace App\Http\Requests\ProductModel;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
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
            'image' => 'required|file|max:1024|mimes:jpg,webp,jpeg,png'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Изображение обязательное!',
            'image.file' => 'Изображение должно быть файлом!',
            'image.max' => 'Максимальнывй вес изображения не больше 1мб!',
            'image.mimes' => 'Допустимые разрешения: jpg,webp,jpeg,png!',
        ];
    }
}
