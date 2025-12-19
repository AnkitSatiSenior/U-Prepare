<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaFile;
use App\Models\SocialSafeguardEntry;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class MediaFileController extends Controller
{
    public function getByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        $files = MediaFile::whereIn('id', $ids)->get([
            'id',
            'path',
            'type', // correct column name
            'meta_data',
            'lat',
            'long',
            'created_at',
            'updated_at',
        ]);

        return response()->json($files);
    }

    // Controller
    public function gallery(Request $request)
    {
        $perPage = 50;
        $page = $request->get('page', 1);
        $search = $request->get('search'); // user search input

        $query = MediaFile::latest();

        // 🔎 Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Match filename from meta_data JSON
                $q->where('meta_data->name', 'like', "%{$search}%")
                    // Match raw path without extension
                    ->orWhereRaw("REPLACE(SUBSTRING_INDEX(path, '/', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(path, '.', -1), '/', -1), '') LIKE ?", ["%{$search}%"])
                    // Match full path (with extension)
                    ->orWhere('path', 'like', "%{$search}%");
            });
        }

        // Paginate
        $filesPaginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Map with extra meta
        $filesWithMeta = $filesPaginator->getCollection()->map(function ($file) {
            $url = '/storage/app/public/' . ltrim($file->path, '/');
            $ext = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

            return [
                'id' => $file->id,
                'url' => $url,
                'thumb' => $isImage ? $url : asset('icons/file-icon.png'),
                'filename' => $file->meta_data['name'] ?? basename($file->path),
                'isImage' => $isImage,
                'extension' => $ext,
                'month' => \Carbon\Carbon::parse($file->created_at)->format('F Y'),
            ];
        });

        // Replace paginator's collection
        $filesPaginator->setCollection($filesWithMeta);

        // Grouped for gallery view
        $filesGrouped = $filesWithMeta->groupBy('month');

        // Flat for modal/carousel
        $allFiles = $filesWithMeta->values();

        return view('admin.media-gallery', compact('filesGrouped', 'allFiles', 'filesPaginator', 'search'));
    }

    public function index()
    {
        $files = MediaFile::latest()
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'src' => Storage::url($file->path),
                    'thumb' => Storage::url($file->path),
                    'name' => $file->meta_data['name'] ?? basename($file->path),
                    'type' => $file->type,
                ];
            });

        return response()->json($files);
    }

    public function store(Request $request)
    {
        $request->validate([
            'social_id' => 'nullable|exists:social_safeguard_entries,id',
            'media_files.*' => 'required|file',
        ]);

        $mediaIds = [];

        foreach ($request->file('media_files') as $file) {
            $path = $file->store('uploads', 'public');
            $media = MediaFile::create([
                'path' => $path,
                'type' => $file->getClientMimeType(),
                'meta_data' => ['name' => $file->getClientOriginalName()],
            ]);
            $mediaIds[] = $media->id;
        }

        if ($request->social_id) {
            $socialEntry = SocialSafeguardEntry::findOrFail($request->social_id);
            $existing = $socialEntry->photos_documents_case_studies ?? [];
            $socialEntry->photos_documents_case_studies = array_merge($existing, $mediaIds);
            $socialEntry->save();
        }

        return redirect()->back()->with('success', 'Files uploaded successfully.');
    }

    public function show($id)
    {
        $media = MediaFile::findOrFail($id);

        return response()->json([
            'id' => $media->id,
            'url' => Storage::url($media->path),
            'name' => $media->meta_data['name'] ?? basename($media->path),
            'type' => $media->type,
            'meta_data' => $media->meta_data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $media = MediaFile::findOrFail($id);
        $request->validate([
            'name' => 'nullable|string|max:255',
            'meta_data' => 'nullable|array',
        ]);

        $meta = $media->meta_data ?? [];
        if ($request->has('name')) {
            $meta['name'] = $request->name;
        }
        if ($request->has('meta_data')) {
            $meta = array_merge($meta, $request->meta_data);
        }

        $media->update(['meta_data' => $meta]);

        return response()->json([
            'status' => 'success',
            'message' => 'Media updated successfully.',
            'media' => $media,
        ]);
    }

    public function destroy($id)
    {
        try {
            $media = MediaFile::findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
                Log::info('Media file deleted from storage', [
                    'media_id' => $id,
                    'path' => $media->path,
                ]);
            } else {
                Log::warning('Media file not found in storage', [
                    'media_id' => $id,
                    'path' => $media->path,
                ]);
            }

            // Remove media ID from related SocialSafeguardEntry records
            $entries = SocialSafeguardEntry::whereJsonContains('photos_documents_case_studies', $media->id)->get();
            foreach ($entries as $entry) {
                $ids = array_diff($entry->photos_documents_case_studies, [$media->id]);
                $entry->photos_documents_case_studies = array_values($ids);
                $entry->save();

                Log::info('Media ID detached from SocialSafeguardEntry', [
                    'entry_id' => $entry->id,
                    'media_id' => $media->id,
                    'remaining_ids' => $entry->photos_documents_case_studies,
                ]);
            }

            // Delete database record
            $media->delete();
            Log::info('Media record deleted from database', ['media_id' => $id]);

            // ✅ Redirect with flash message
            return redirect()->back()->with('success', 'Media deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Media not found for deletion', [
                'media_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Media not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting media', [
                'media_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to delete media. Please try again.');
        }
    }

    public function deleteMedia($id)
    {
        try {
            $file = MediaFile::findOrFail($id);

            // Delete physical file if it exists
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            // Delete DB record
            $file->delete();

            return redirect()->back()->with('success', 'File deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'File not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete file. Please try again.');
        }
    }

    public function upload(Request $request)
{
    try {
        // ✅ Strict validation (JSON safe)
        $validated = $request->validate([
            'social_id' => 'required|exists:social_safeguard_entries,id',
            'media_files' => 'required|array|min:1',
            'media_files.*' => 'file|max:10240', // 10MB
        ]);

        $socialEntry = SocialSafeguardEntry::findOrFail($validated['social_id']);

        $existingMediaIds = $socialEntry->photos_documents_case_studies ?? [];
        $newMediaIds = [];
        $uploadedFiles = [];

        foreach ($request->file('media_files') as $file) {
            $path = $file->store('uploads', 'public');

            $media = MediaFile::create([
                'path' => $path,
                'type' => $file->getClientMimeType(),
                'meta_data' => [
                    'name' => $file->getClientOriginalName(),
                ],
            ]);

            $newMediaIds[] = $media->id;

            // ✅ Only NEW files returned to frontend
            $uploadedFiles[] = [
                'id' => $media->id,
                'url' => Storage::url($media->path),
                'name' => $media->meta_data['name'],
                'type' => $media->type,
                'meta_data' => $media->meta_data,
            ];
        }

        // ✅ Merge old + new IDs (no duplicates)
        $socialEntry->photos_documents_case_studies = array_values(
            array_unique(array_merge($existingMediaIds, $newMediaIds))
        );
        $socialEntry->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Files uploaded successfully.',
            'social_id' => $socialEntry->id,
            'files' => $uploadedFiles, // 👈 frontend expects THIS
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->errors()['media_files'][0] ?? 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {
        \Log::error('Upload failed', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Upload failed. Please try again.',
        ], 500);
    }
}

}
