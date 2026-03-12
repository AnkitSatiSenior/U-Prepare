<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialProgressUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'finance_amount',
        'no_of_bills',
        'bill_serial_no',
        'submit_date',
        'media' // Stores JSON array of MediaFile IDs: [1, 2, 3]
    ];

    protected $casts = [
        'bill_serial_no' => 'array',
        // ✅ Native Laravel Cast: This guarantees $this->media is always an array or null.
        'media'          => 'array', 
        'submit_date    ' => 'date',
        'finance_amount ' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function project()
    {
        return $this->belongsTo(SubPackageProject::class, 'project_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeForProjectOnDate($query, int $projectId, string $date)
    {
        return $query->where('project_id', $projectId)
                     ->whereDate('submit_date', $date)
                     ->orderBy('submit_date', 'desc');
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->whereHas('project.packageProject', fn($q) =>
            $q->where('department_id', $departmentId)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    /**
     * Get the associated MediaFile models.
     * * 🚨 ARCHITECTURE WARNING: 
     * Calling `$update->media_files` inside a loop will trigger an N+1 database query.
     * Only use this accessor for single-model retrievals (e.g., showing details of ONE update).
     * For collections, use the batched O(1) in-memory extraction method we built in the Controller.
     */
    public function getMediaFilesAttribute()
    {
        $ids = $this->media ?? [];

        if (empty($ids)) {
            return collect();
        }

        // Returns MediaFile collection. 
        // These models will inherently have their S3 URLs attached via their own HasS3Image trait.
        return MediaFile::whereIn('id', $ids)->get();
    }
}