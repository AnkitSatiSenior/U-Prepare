<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication is handled by routes middleware
    }

    public function rules(): array
    {
        return [
            'dob' => ['nullable', 'date'],
            'date_of_joining' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', 'max:1000'],
            'total_work_experience' => ['nullable', 'string', 'max:100'],
            'area_of_expertise' => ['nullable', 'string'],
            'research_publication_citation' => ['nullable', 'string', 'max:500'],
            'previous_experience' => ['nullable', 'string'],
        ];
    }
}