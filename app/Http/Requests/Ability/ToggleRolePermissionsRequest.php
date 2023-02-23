<?php

namespace App\Http\Requests\Ability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleRolePermissionsRequest extends FormRequest
{
    const LIST = 'list';
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';
    protected $types = [self::LIST, self::CREATE, self::EDIT, self::DELETE];
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
            'type' => 'required', Rule::in($this->types)
        ];
    }
}
