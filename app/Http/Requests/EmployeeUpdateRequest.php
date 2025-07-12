<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
            'full_name'      => 'sometimes|required|string|max:255',
            'email'          => 'sometimes|required|email|unique:employees,email,' . $this->employee,
            'phone'          => 'sometimes|required|string|max:20',
            'department_id'  => 'sometimes|required|exists:departments,id',
            'position_id'    => 'sometimes|required|exists:positions,id',
            'gender'         => 'sometimes|required|in:male,female',
            'status'         => 'nullable|in:active,inactive,terminated',
            'birth_date'     => 'sometimes|required|date',
            'hire_date'      => 'sometimes|required|date',
            'salary'         => 'nullable|numeric',
            'national_id'    => 'nullable|string|unique:employees,national_id,' . $this->employee,
            'address'        => 'nullable|string',
            'notes'          => 'nullable|string',
        ];
    }
}
