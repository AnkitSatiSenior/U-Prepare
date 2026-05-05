<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.required' => 'Please choose a profile photo to upload.',
            'profile_photo.image' => 'The selected file must be an image.',
            'profile_photo.mimes' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.',
            'profile_photo.max' => 'The profile photo must not be larger than 2 MB.',
        ];
    }
}
