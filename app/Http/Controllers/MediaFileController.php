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

        // Optimization: Prevent an empty database query if no IDs are passed
        if (empty($ids)) {
            return response()->json([]);
        }

        // The model automatically appends the 'url' attribute via HasS3Image trait
        $files = MediaFile::whereIn('id', $ids)->get([
            'id',
            'path',
            'type',
            'remark', // Added the remark column here
            'meta_data',
            'lat',
            'long',
            'created_at',
            'updated_at',
        ]);

        return response()->json($files);
    }

    public function gallery(Request $request)
    {
        $perPage = 50;
        $page = $request->get('page', 1);
        $search = $request->get('search');

        $query = MediaFile::latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('meta_data->name', 'like', "%{$search}%")
                    ->orWhereRaw("REPLACE(SUBSTRING_INDEX(path, '/', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(path, '.', -1), '/', -1), '') LIKE ?", ["%{$search}%"])
                    ->orWhere('path', 'like', "%{$search}%");
            });
        }

        $filesPaginator = $query->paginate($perPage, ['*'], 'page', $page);

        $filesWithMeta = $filesPaginator->getCollection()->map(function ($file) {
            // ✅ S3 FIX: Utilize the appended 'url' from the HasS3Image trait instead of local hardcoded paths
            $ext = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

            return [
                'id' => $file->id,
                'url' => $file->url, 
                'thumb' => $isImage ? $file->url : asset('icons/file-icon.png'),
                'filename' => $file->meta_data['name'] ?? basename($file->path),
                'isImage' => $isImage,
                'extension' => $ext,
                'month' => Carbon::parse($file->created_at)->format('F Y'),
            ];
        });

        $filesPaginator->setCollection($filesWithMeta);

        $filesGrouped = $filesWithMeta->groupBy('month');
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
                    // ✅ S3 FIX: Use the model's appended URL property
                    'src' => $file->url, 
                    'thumb' => $file->url,
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
            // ✅ S3 FIX: Store directly to the 's3' disk
            $path = $file->store('uploads', 's3');
            
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
            // ✅ S3 FIX: Use the model's appended URL property
            'url' => $media->url,
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

            // ✅ S3 FIX: Explicitly targeting S3 disk for deletion
            if (Storage::disk('s3')->exists($media->path)) {
                Storage::disk('s3')->delete($media->path);
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

            $media->delete();
            Log::info('Media record deleted from database', ['media_id' => $id]);

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

            // ✅ S3 FIX: Explicitly targeting S3 disk for deletion
            if (Storage::disk('s3')->exists($file->path)) {
                Storage::disk('s3')->delete($file->path);
            }

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
        $validated = $request->validate([
            'social_id'   => 'required|exists:social_safeguard_entries,id',
            'media_files' => 'required|array|min:1',
            'media_files.*' => 'file|max:10240',
            // Added: Validation for remarks, matching the index of media_files
            'remarks'     => 'nullable|array',
            'remarks.*'   => 'nullable|string|max:5000',
        ]);

        $socialEntry = SocialSafeguardEntry::findOrFail($validated['social_id']);

        $existingMediaIds = $socialEntry->photos_documents_case_studies ?? [];
        $newMediaIds = [];
        $uploadedFiles = [];

        // We use the index ($key) to map the specific remark to the specific file
        foreach ($request->file('media_files') as $key => $file) {
            
            // ✅ S3 STORAGE: Direct upload to S3
            $path = $file->store('uploads', 's3');

            // ✅ REMARK LOGIC: Extract remark for this specific file index
            $remark = $request->input("remarks.$key");

            $media = MediaFile::create([
                'path'      => $path,
                'type'      => $file->getClientMimeType(),
                'remark'    => $remark, // Now persisted to DB
                'meta_data' => [
                    'name' => $file->getClientOriginalName(),
                ],
            ]);

            $newMediaIds[] = $media->id;

            $uploadedFiles[] = [
                'id'        => $media->id,
                'url'       => $media->url,
                'name'      => $media->meta_data['name'],
                'type'      => $media->type,
                'remark'    => $media->remark,
                'meta_data' => $media->meta_data,
            ];
        }

        // Atomic update of the JSON column in the parent entry
        $socialEntry->photos_documents_case_studies = array_values(
            array_unique(array_merge($existingMediaIds, $newMediaIds))
        );
        $socialEntry->save();

        return response()->json([
            'status'    => 'success',
            'message'   => 'Files and remarks uploaded successfully.',
            'social_id' => $socialEntry->id,
            'files'     => $uploadedFiles,
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {
        Log::error('Upload failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Upload failed. Please try again.',
        ], 500);
    }
}
}