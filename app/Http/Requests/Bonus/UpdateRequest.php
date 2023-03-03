<?php

namespace App\Http\Requests\Bonus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'is_active' => 'nullable|boolean',
            'coefficient_conversion' => 'nullable|numeric|min:0',
            'register_bonuses' => 'nullable|integer|min:0',
            'allow_discounted' => 'nullable|boolean',
            'allow_kits' => 'nullable|boolean',
            'max_discount_percents' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'bonus_percent' => 'nullable|integer|min:0',
            'groups' => [
                Rule::when(fn($input) =>  $input['groups'] === 'without_groups' || $input['groups'] === 'all', ['string']),
                Rule::when(fn($input) =>  is_array($input['groups']), [ 'array'])
            ],
            'groups.*' => 'required|integer|exists:groups,id',
            'categories' => 'array',
            'categories.*' => 'required|integer|exists:categories,id',
        ];
    }
}
