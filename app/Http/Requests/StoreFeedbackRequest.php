<?php

// app/Http/Requests/StoreFeedbackRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:150'],
            // Indian 10-digit format: Starts with 6-9, followed by 9 digits
            'phone_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'type'         => ['required', 'in:inquiry,feedback,others'],
            'subject'      => ['nullable', 'string', 'max:200'],
            'message'      => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Please enter a valid 10-digit Indian mobile number.',
        ];
    }
}
