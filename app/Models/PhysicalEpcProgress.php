<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalEpcProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'physical_epc_progress';

    protected $fillable = [
        'epcentry_data_id',
        'percent',
        'amount',
        'items',
        'progress_submitted_date',
        'images', // JSON array of media_file IDs
    ];

    protected $casts = [
        'progress_submitted_date' => 'date',
        'images' => 'array',
    ];

    public function epcEntryData()
    {
        return $this->belongsTo(EpcEntryData::class, 'epcentry_data_id');
    }

    /**
     * Accessor: get MediaFile models linked via IDs stored in `images`.
     */
    public function getMediaFilesAttribute()
    {
        if (empty($this->images)) {
            return collect();
        }
        return MediaFile::whereIn('id', $this->images)->get();
    }
}
