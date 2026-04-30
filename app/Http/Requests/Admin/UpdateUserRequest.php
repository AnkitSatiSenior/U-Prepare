<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'username' => 'required|alpha_dash|max:50|unique:users,username,' . $this->user->id,
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'sub_department_id' => 'nullable|exists:sub_departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'gender' => 'nullable|in:male,female,other',
            'phone_no' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'dob' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'qualification' => 'nullable|string',
            'total_work_experience' => 'nullable|string|max:50',
            'area_of_expertise' => 'nullable|string',
            'procurement_support' => 'nullable|string',
            'research_publication_citation' => 'nullable|string|max:100',
            'previous_experience' => 'nullable|string',
        ];
    }
}
