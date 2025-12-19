<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafeguardEntry extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'sub_package_project_id',
        'safeguard_compliance_id',
        'contraction_phase_id',
        'already_define_safeguard_entry_id',
        'sl_no',
        'is_validity',
        'is_major_head',
        'order_by',
        'is_parent',
    ];

    protected $casts = [
        'is_validity'   => 'boolean',
        'is_major_head' => 'boolean',
        'is_parent'     => 'boolean',  // ✅ NEW CAST
        'order_by'      => 'integer',
    ];

    /* ---------------------------------------------
       RELATIONSHIPS
    --------------------------------------------- */
  public function definedSafeguard()
    {
        return $this->belongsTo(
            AlreadyDefineSafeguardEntry::class,
            'already_define_safeguard_entry_id'
        );
    }
    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class);
    }

    public function safeguardCompliance()
    {
        return $this->belongsTo(SafeguardCompliance::class);
    }

    public function contractionPhase()
    {
        return $this->belongsTo(ContractionPhase::class);
    }

    /**
     * Latest future entry (>= today)
     */
    public function socialSafeguardEntry()
    {
        return $this->hasOne(SocialSafeguardEntry::class)
            ->whereDate('date_of_entry', '>=', now()->toDateString())
            ->latest('date_of_entry');
    }

    /**
     * All related social entries
     */
    public function socialSafeguardEntries()
    {
        return $this->hasMany(SocialSafeguardEntry::class, 'safeguard_entry_id');
    }

    /**
     * Get the latest social entry before or on a given date.
     */
    public function latestSocialEntry($selectedDate = null)
    {
        $selectedDate = $selectedDate ?? now()->toDateString();

        return $this->socialSafeguardEntries
            ->filter(fn ($entry) =>
                $entry->date_of_entry->format('Y-m-d') <= $selectedDate
            )
            ->sortByDesc('date_of_entry')
            ->first();
    }
}
