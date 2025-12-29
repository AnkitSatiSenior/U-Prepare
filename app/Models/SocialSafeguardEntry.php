<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SocialSafeguardEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['already_define_safeguard_entry_id', 'sub_package_project_id', 'social_compliance_id', 'contraction_phase_id', 'yes_no', 'photos_documents_case_studies', 'remarks', 'validity_date', 'date_of_entry'];

    protected $casts = [
        'photos_documents_case_studies' => 'array',
        'validity_date' => 'date',
        'date_of_entry' => 'date',
        'yes_no' => 'integer', // <-- ensure it's always an integer
    ];
    public function masterSafeguard()
    {
        return $this->belongsTo(AlreadyDefineSafeguardEntry::class, 'already_define_safeguard_entry_id');
    }
    public function safeguardEntry()
    {
        return $this->belongsTo(SafeguardEntry::class);
    }

    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class);
    }

    public function socialCompliance()
    {
        return $this->belongsTo(SafeguardCompliance::class, 'social_compliance_id');
    }

    public function contractionPhase()
    {
        return $this->belongsTo(ContractionPhase::class);
    }

    public function latestSocialEntry($date = null)
    {
        $date = $date ?? now()->format('Y-m-d');

        return $this->safeguardEntry->socialSafeguardEntry
            ->filter(function ($entry) use ($date) {
                return $entry->date_of_entry->format('Y-m-d') <= $date;
            })
            ->sortByDesc('date_of_entry')
            ->first();
    }

    public function getIsLockedAttribute(): bool
    {
        $hasValidity = $this->safeguardEntry->is_validity && $this->validity_date;

        if ($this->contractionPhase->is_one_time) {
            if ($hasValidity) {
                return Carbon::parse($this->validity_date)->isFuture();
            }
            return true;
        }

        if ($hasValidity) {
            return Carbon::parse($this->validity_date)->isFuture();
        }

        return false;
    }
    public function getMediaFilesAttribute()
    {
        $ids = $this->photos_documents_case_studies ?? [];
        return MediaFile::whereIn('id', $ids)->get();
    }
}
