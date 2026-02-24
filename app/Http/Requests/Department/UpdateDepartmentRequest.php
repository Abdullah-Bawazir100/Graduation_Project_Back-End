<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
        $departmentId = $this->route('id'); // Assuming the route parameter is named 'id'
        return [
            'name' => 'required|string|max:255|unique:departments,name,' . $departmentId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'اسم القسم لا يمكن ان يزيد عن 255 حرفا.',
            'name.unique' => 'اسم القسم مسجل مسبقاً.',
        ];
    }
}
