<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\Profile\UpdateUserProfileRequest;

class UserProfileController extends Controller
{
    public function edit(User $user)
    {
        // Notice we are pointing to a new dedicated 'profile' view folder
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request, User $user)
    {
        $user->update($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Profile data for {$user->name} updated successfully.");
    }
}