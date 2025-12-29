<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalBoqProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'boq_entry_id',
        'qty',
        'amount',
        'progress_submitted_date',
        'sub_package_project_id',
        'media', // JSON array of media_file IDs
        'lat',
        'long',
    ];

    protected $casts = [
        'media' => 'array', // JSON cast
        'progress_submitted_date' => 'date',
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
    ];

    public function boqEntry()
    {
        return $this->belongsTo(BoqEntryData::class, 'boq_entry_id');
    }
    public function boqEntryData()
    {
        return $this->belongsTo(BoqEntryData::class, 'boq_entry_id');
    }

    /**
     * Accessor: get MediaFile models linked via IDs stored in `media`.
     */
    public function getMediaFilesAttribute()
    {
        if (empty($this->media)) {
            return collect();
        }
        return MediaFile::whereIn('id', $this->media)->get();
    }
}
