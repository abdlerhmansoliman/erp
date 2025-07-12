<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'gender' => 'required|in:male,female',
            'status' => 'nullable|in:active,inactive,terminated',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'national_id' => 'nullable|string|unique:employees,national_id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
