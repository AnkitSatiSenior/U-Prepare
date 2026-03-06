<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class NewsService
{
    private const DISK = 's3';
    private const UPLOAD_PATH = 'uploads/news';

    /**
     * Create a new News entry with robust S3 upload.
     */
    public function createNews(array $data, ?UploadedFile $file, ?string $ipAddress): News
    {
        return DB::transaction(function () use ($data, $file, $ipAddress) {
            $data['ip_address'] = $ipAddress;

            if ($file) {
                $data['file'] = $this->uploadToS3($file);
            }

            return News::create($data);
        });
    }

    /**
     * Update an existing News entry and manage S3 file replacement.
     */
    public function updateNews(News $news, array $data, ?UploadedFile $file): bool
    {
        return DB::transaction(function () use ($news, $data, $file) {
            if ($file) {
                $this->deleteFromS3($news->file);
                $data['file'] = $this->uploadToS3($file);
            }

            return $news->update($data);
        });
    }

    /**
     * Delete the News entry and its associated S3 file.
     */
    public function deleteNews(News $news): bool
    {
        return DB::transaction(function () use ($news) {
            $this->deleteFromS3($news->file);
            return $news->delete();
        });
    }

    /**
     * Handle S3 Upload with error logging.
     */
    private function uploadToS3(UploadedFile $file): string
    {
        try {
            $path = $file->store(self::UPLOAD_PATH, self::DISK);
            if (!$path) {
                throw new Exception("S3 upload returned false.");
            }
            return $path;
        } catch (Exception $e) {
            Log::error('S3 Upload Failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to upload file to storage. Please try again.');
        }
    }

    /**
     * Handle S3 Deletion safely.
     */
    private function deleteFromS3(?string $filePath): void
    {
        if (!$filePath) return;

        try {
            if (Storage::disk(self::DISK)->exists($filePath)) {
                Storage::disk(self::DISK)->delete($filePath);
            }
        } catch (Exception $e) {
            Log::error('S3 Deletion Failed for path [' . $filePath . ']: ' . $e->getMessage());
            // We do not throw here to prevent halting the DB deletion 
            // if the file is already missing or S3 has a temporary hiccup.
        }
    }
}