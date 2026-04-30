<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'profile_photo_path' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username,' . $userId],
            'email' => ['required', 'email', 'unique:users,email,' . $userId],
            'dob' => ['nullable', 'date'],
            'date_of_joining' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', 'max:1000'],
            'total_work_experience' => ['nullable', 'string', 'max:100'],
            'area_of_expertise' => ['nullable', 'string'],
            'research_publication_citation' => ['nullable', 'string', 'max:500'],
            'previous_experience' => ['nullable', 'string'],
        ];
    }

    /**
     * Executes the profile update and handles S3 atomic swapping.
     */
    public function execute()
    {
        $user = $this->route('user');
        $updateData = $this->validated();
        $photo = $this->file('profile_photo_path');

        return DB::transaction(function () use ($user, $updateData, $photo) {
            $newPath = null;
            $oldPath = null;

            // Handle S3 File Swapping
            if ($photo) {
                // 1. Upload new photo first
                $newPath = $photo->store('profile-photos', 's3');
                $updateData['profile_photo_path'] = $newPath;
                
                // 2. Identify old photo for deletion
                $oldPath = $user->profile_photo_path;
            }

            try {
                // Update the database record
                $user->update($updateData);

                // 3. Only delete the old photo from S3 IF the database update succeeded
                if ($oldPath && Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }

                return $user;

            } catch (Exception $e) {
                Log::error('User profile update failed: ' . $e->getMessage(), [
                    'user_id' => $user->id
                ]);
                
                // If DB failed but we uploaded a new photo, clean up the newly uploaded orphaned file
                if ($newPath && Storage::disk('s3')->exists($newPath)) {
                    Storage::disk('s3')->delete($newPath);
                }
                
                throw $e;
            }
        });
    }
}