<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class CreateEditRequest extends FormRequest
{
//    public function withValidator($validator)
//    {
//        $validator->after(function ($validator) {
//            if ($validator->fails()) {
//                return response()->json(['message' => 'validation error']);
//            }
//            return null;
//        });
//    }
//    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        return response()->json(['message' => 'validation error']);
//    }
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
            'phone' => 'required|integer',
            'email' => 'string'
        ];
    }
}
