<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    /**
     * Update an existing user and manage S3 photo replacement.
     */
    public function updateUser(User $user, array $data, ?UploadedFile $photo): User
    {
        return DB::transaction(function () use ($user, $data, $photo) {
            
            // Map the easily assignable data
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'] ?? $user->username,
                'role_id' => $data['role_id'],
                'department_id' => $data['department_id'] ?? null,
                'sub_department_id' => $data['sub_department_id'] ?? null,
                'designation_id' => $data['designation_id'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone_no' => $data['phone_no'] ?? null,
                'district' => $data['district'] ?? null,
                'status' => $data['status'],
                'dob' => $data['dob'] ?? null,
                'date_of_joining' => $data['date_of_joining'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'total_work_experience' => $data['total_work_experience'] ?? null,
                'area_of_expertise' => $data['area_of_expertise'] ?? null,
                'procurement_support' => $data['procurement_support'] ?? null,
                'research_publication_citation' => $data['research_publication_citation'] ?? null,
                'previous_experience' => $data['previous_experience'] ?? null,
            ];

            // Conditionally hash password
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            // Handle S3 File Swapping
            if ($photo) {
                // 1. Upload new photo first
                $newPath = $photo->store('profile-photos', 's3');
                $updateData['profile_photo_path'] = $newPath;
                
                // 2. Identify old photo for deletion
                $oldPath = $user->profile_photo_path;
            }

            try {
                $user->update($updateData);

                // 3. Only delete the old photo from S3 IF the database update succeeded
                if (isset($oldPath) && $oldPath && Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }

                return $user;

            } catch (Exception $e) {
                Log::error('User update failed: ' . $e->getMessage());
                
                // If DB failed but we uploaded a new photo, clean up the newly uploaded orphaned file
                if (isset($newPath)) {
                    Storage::disk('s3')->delete($newPath);
                }
                
                throw $e;
            }
        });
    }
}
