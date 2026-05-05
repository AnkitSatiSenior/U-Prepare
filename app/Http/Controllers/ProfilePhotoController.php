<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilePhotoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProfilePhotoController extends Controller
{
    public function update(UpdateProfilePhotoRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $disk = config('jetstream.profile_photo_disk', config('filesystems.default', 'public'));
        $photo = $request->file('profile_photo');
        $filename = Str::uuid()->toString().'.'.$photo->getClientOriginalExtension();
        $path = 'profile-photos/'.$filename;
        $oldPath = $user->profile_photo_path;

        Storage::disk($disk)->putFileAs('profile-photos', $photo, $filename);

        try {
            DB::transaction(function () use ($user, $path) {
                $user->forceFill([
                    'profile_photo_path' => $path,
                ])->save();
            });

            if ($oldPath && $oldPath !== $path && Storage::disk($disk)->exists($oldPath)) {
                Storage::disk($disk)->delete($oldPath);
            }
        } catch (Throwable $exception) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            Log::error('Profile photo update failed.', [
                'user_id' => $user->id,
                'disk' => $disk,
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $user->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile photo updated successfully.',
                'photo_url' => $user->profile_photo_url,
                'photo_path' => $user->profile_photo_path,
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }
}
