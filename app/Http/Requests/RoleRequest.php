<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handle authorization in controller middleware
    }
    
    public function rules(): array
    {
        $roleId = $this->route('role'); // For update requests
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/', // Only alphanumeric, spaces, hyphens, underscores
                Rule::unique('roles', 'name')->ignore($roleId)
            ],
            'guard_name' => 'sometimes|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|exists:permissions,name'
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique' => 'This role name already exists.',
            'name.regex' => 'Role name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.'
        ];
    }
    
    public function attributes(): array
    {
        return [
            'name' => 'role name',
            'permissions' => 'permissions',
            'permissions.*' => 'permission'
        ];
    }
    
    protected function prepareForValidation(): void
    {
        // Clean up the name field
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(strtolower($this->name))
            ]);
        }
    }
}