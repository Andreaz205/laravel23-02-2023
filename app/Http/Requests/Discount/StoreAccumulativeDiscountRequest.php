<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccumulativeDiscountRequest extends FormRequest
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
            'value' => 'required|integer|min:0',
            'allow_discounted' => 'required|boolean',
            'allow_kits' => 'required|boolean',
            'threshold' => 'required|integer|min:0',
            'groups' => 'nullable|array',
            'groups.*' => 'required|integer|exists:groups,id',
            'categories' => 'nullable|array',
            'categories.*' => 'required|integer|exists:categories,id',
        ];
    }
}
