<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProcurementWorkProgram extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'package_project_id',
        'procurement_details_id',
        'name_work_program',
        'weightage',
        'days',
        'start_date',
        'planned_date',
        'procurement_bid_document',
        'pre_bid_minutes_document',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date'   => 'date',
        'planned_date' => 'date',
        'weightage'    => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the related PackageProject.
     */
    public function packageProject()
    {
        return $this->belongsTo(PackageProject::class);
    }

    /**
     * Get the related ProcurementDetail.
     */
    public function procurementDetail()
    {
        return $this->belongsTo(ProcurementDetail::class, 'procurement_details_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the full URL for the procurement bid document.
     *
     * @return string|null
     */
    public function getProcurementBidDocumentUrlAttribute()
    {
        return $this->procurement_bid_document
            ? Storage::url($this->procurement_bid_document)
            : null;
    }

    /**
     * Get the full URL for the pre-bid minutes document.
     *
     * @return string|null
     */
    public function getPreBidMinutesDocumentUrlAttribute()
    {
        return $this->pre_bid_minutes_document
            ? Storage::url($this->pre_bid_minutes_document)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to include work programs with the same package_project_id or procurement_details_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $packageId
     * @param int $procurementId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSamePackageOrProcurement($query, int $packageId, int $procurementId)
    {
        return $query->where('package_project_id', $packageId)
                     ->orWhere('procurement_details_id', $procurementId);
    }

    /**
     * Scope a query to include work programs with the same package_project_id and procurement_details_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $packageId
     * @param int $procurementId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSamePackageAndProcurement($query, int $packageId, int $procurementId)
    {
        return $query->where('package_project_id', $packageId)
                     ->where('procurement_details_id', $procurementId);
    }

    /**
     * Scope a query to include work programs for a specific package.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $packageProjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPackage($query, int $packageProjectId)
    {
        return $query->where('package_project_id', $packageProjectId);
    }
}
