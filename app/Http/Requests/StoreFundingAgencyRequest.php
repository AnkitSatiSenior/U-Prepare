<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFundingAgencyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $agencyId = $this->route('funding_agency') ? $this->route('funding_agency')->id : null;

        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('funding_agencies')->ignore($agencyId)
            ],
            'code' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ];
    }
}