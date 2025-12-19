<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlreadyDefineSafeguardEntry extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'already_define_safeguard_entries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'safeguard_compliance_id',
        'contraction_phase_id',
        'category_id',
        'sl_no',
        'order_by',
        'item_description',

        'is_validity',
        'is_major_head',
        'is_parent',    // ✅ NEW FIELD ADDED
    ];

    /**
     * Cast attributes to appropriate types.
     */
    protected $casts = [
        'is_validity' => 'boolean',
        'is_major_head' => 'boolean',
        'is_parent' => 'boolean',  // ✅ NEW CAST ADDED

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** Safeguard Compliance relationship */
    public function safeguardCompliance()
    {
        return $this->belongsTo(SafeguardCompliance::class, 'safeguard_compliance_id');
    }

    /** Contraction Phase relationship */
    public function contractionPhase()
    {
        return $this->belongsTo(ContractionPhase::class, 'contraction_phase_id');
    }

    /** Category (sub_category table) relationship */
    public function category()
    {
        return $this->belongsTo(SubCategory::class, 'category_id');
    }
}
