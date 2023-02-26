<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'name' => 'string|max:255',
            'content' => 'string|max:255',
            'images_for_delete' => 'array',
            'images_for_delete.*' => 'integer',
            'answer' => 'nullable|array',
            'answer.content' => 'required|string',
            'answer.admin_id' => 'required|integer|exists:admins,id',
        ];
    }
}
