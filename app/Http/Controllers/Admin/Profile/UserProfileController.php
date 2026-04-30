<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\Profile\UpdateUserProfileRequest;

class UserProfileController extends Controller
{
    public function edit(User $user)
    {
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request, User $user)
    {
        // Trigger the execution logic built into the Request
        $request->execute();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Profile data for {$user->name} updated successfully.");
    }
}