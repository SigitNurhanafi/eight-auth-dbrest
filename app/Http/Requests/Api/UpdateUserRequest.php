<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => [
                'sometimes',
                'string',
                'in:admin,user',
        // Only admin can update roles
                function ($attribute, $value, $fail) {
            if ($value && !$this->user()->isAdmin()) {
                $fail('Only admins can change user roles.');
            }
        }
            ],
        ];
    }
}
