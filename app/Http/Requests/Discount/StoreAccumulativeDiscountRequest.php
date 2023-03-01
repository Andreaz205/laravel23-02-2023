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
            'description' => 'nullable|string:max:255',
            'value' => 'required|integer|min:0',
            'allow_discounted' => 'required|boolean',
            'allow_kits' => 'required|boolean',
            'threshold' => 'required|integer|min:0',
            'groups' => [
                Rule::when(fn($input) =>  $input['groups'] === 'without_groups', ['string']),
                Rule::when(fn($input) =>  is_array($input['groups']), ['array'])
            ],
            'groups.*' => 'required|integer|exists:groups,id',
            'categories' => 'array',
            'categories.*' => 'required|integer|exists:categories,id',
        ];
    }
}
