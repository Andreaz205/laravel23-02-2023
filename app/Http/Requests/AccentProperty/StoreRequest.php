<?php

namespace App\Http\Requests\AccentProperty;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'unique:accent_properties,title'],
            'description' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mp3,avi,gif', 'max:10240'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле название обязательно',
            'title.string' => 'Поле название должно быть строкой',
            'title.max' => 'Максимальная длина название не может быть больше 255 символов',
            'title.unique' => 'Такое название уже существует',
            'description.required' => 'Описание обязательно',
            'description.string' => 'Описание должно быть строкой',
            'description.max' => 'Максимальная длина описание не может быть больше 255 символов',
            'file.required' => 'Медиа обязательно',
            'file.file' => 'Медиа должно быть файлом',
            'file.mimes' => 'Доступные разрешения для загрузки jpg,jpeg,png,webp,mp4,mp3,avi,gif',
            'file.max' => 'Максимальный размер файла не может быть больше 10240 ',
        ];
    }
}
