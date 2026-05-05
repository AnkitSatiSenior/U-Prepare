<?php
namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'date_of_joining' => ['nullable', 'date'],
            'total_work_experience' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:1000'],
            'area_of_expertise' => ['nullable', 'string', 'max:2000'],
            'research_publication_citation' => ['nullable', 'string', 'max:1000'],
            'previous_experience' => ['nullable', 'string', 'max:5000'],
        ])->validateWithBag('updateProfileInformation');

        if (! empty($validated['photo'])) {
            $user->updateProfilePhoto($validated['photo']);
        }

        $profileData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'phone_no' => $this->nullableValue($validated, 'phone_no'),
            'dob' => $this->nullableValue($validated, 'dob'),
            'date_of_joining' => $this->nullableValue($validated, 'date_of_joining'),
            'total_work_experience' => $this->nullableValue($validated, 'total_work_experience'),
            'qualification' => $this->nullableValue($validated, 'qualification'),
            'area_of_expertise' => $this->nullableValue($validated, 'area_of_expertise'),
            'research_publication_citation' => $this->nullableValue($validated, 'research_publication_citation'),
            'previous_experience' => $this->nullableValue($validated, 'previous_experience'),
        ];

        if ($validated['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $profileData);
        } else {
            $user->forceFill($profileData)->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'username' => $input['username'],
            'phone_no' => $input['phone_no'],
            'dob' => $input['dob'],
            'date_of_joining' => $input['date_of_joining'],
            'total_work_experience' => $input['total_work_experience'],
            'qualification' => $input['qualification'],
            'area_of_expertise' => $input['area_of_expertise'],
            'research_publication_citation' => $input['research_publication_citation'],
            'previous_experience' => $input['previous_experience'],
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    /**
     * Convert empty string form values into null before persisting them.
     */
    protected function nullableValue(array $validated, string $key): mixed
    {
        $value = $validated[$key] ?? null;

        return $value === '' ? null : $value;
    }
}
