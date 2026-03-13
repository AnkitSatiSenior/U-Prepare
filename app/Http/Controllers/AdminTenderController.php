<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminTenderController extends Controller
{
    public function index()
    {
        $tenders = Tender::latest()->paginate(10);
        return view('admin.tenders.index', compact('tenders'));
    }

    public function publicIndex()
    {
        $tenders = Tender::latest()->get();
        return view('tenders.index', compact('tenders'));
    }

    public function create()
    {
        return view('admin.tenders.create');
    }

    /**
     * Store a newly created tender.
     * Enforced DB Transactions and S3 Cleanup on failure.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTender($request);
        $validated['ip_address'] = $request->ip();

        $uploadedPath = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('file')) {
                // Upload strictly to S3
                $uploadedPath = $request->file('file')->store('uploads/tenders', 's3');
                
                if (!$uploadedPath) {
                    throw new \Exception("Failed to upload tender file to S3.");
                }
                
                $validated['file'] = $uploadedPath;
            }

            Tender::create($validated);

            DB::commit();

            return redirect()->route('admin.tenders.index')
                             ->with('success', 'Tender created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tender Creation Failed: ' . $e->getMessage());

            // Cleanup orphaned S3 file if DB insert failed
            if ($uploadedPath && Storage::disk('s3')->exists($uploadedPath)) {
                Storage::disk('s3')->delete($uploadedPath);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create tender. Please try again.']);
        }
    }

    public function edit(Tender $tender)
    {
        return view('admin.tenders.edit', compact('tender'));
    }

    /**
     * Update the specified tender.
     * Prevents data loss by deleting old S3 files ONLY after DB commit.
     */
    public function update(Request $request, Tender $tender)
    {
        $validated = $this->validateTender($request);
        
        $oldFile = $tender->file;
        $newUploadedPath = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('file')) {
                // Upload new file to S3
                $newUploadedPath = $request->file('file')->store('uploads/tenders', 's3');
                
                if (!$newUploadedPath) {
                    throw new \Exception("Failed to upload new tender file to S3.");
                }

                $validated['file'] = $newUploadedPath;
            }

            $tender->update($validated);

            DB::commit();

            // ONLY delete the old file AFTER the database transaction is successfully committed
            if ($newUploadedPath && $oldFile && Storage::disk('s3')->exists($oldFile)) {
                Storage::disk('s3')->delete($oldFile);
            }

            return redirect()->route('admin.tenders.index')
                             ->with('success', 'Tender updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tender Update Failed: ' . $e->getMessage());

            // Cleanup the NEWLY uploaded file to prevent S3 bloating, since DB update failed
            if ($newUploadedPath && Storage::disk('s3')->exists($newUploadedPath)) {
                Storage::disk('s3')->delete($newUploadedPath);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to update tender. Please try again.']);
        }
    }

    /**
     * Remove the specified tender.
     */
    public function destroy(Tender $tender)
    {
        $fileToDelete = $tender->file;

        try {
            DB::beginTransaction();

            $tender->delete();

            DB::commit();

            // Only delete from S3 AFTER successful database deletion
            if ($fileToDelete && Storage::disk('s3')->exists($fileToDelete)) {
                Storage::disk('s3')->delete($fileToDelete);
            }

            return redirect()->route('admin.tenders.index')
                             ->with('success', 'Tender deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tender Deletion Failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete tender.']);
        }
    }

    /**
     * Extracted validation logic to comply with DRY principles.
     * Future optimization: Move this to a dedicated FormRequest class.
     */
    private function validateTender(Request $request): array
    {
        return $request->validate([
            'title_en'      => 'required|string|max:255',
            'title_hi'      => 'required|string|max:255',
            'description_en'=> 'nullable|string',
            'description_hi'=> 'nullable|string',
            'open_date'     => 'required|date',
            'close_date'    => 'required|date|after_or_equal:open_date',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
        ]);
    }
}