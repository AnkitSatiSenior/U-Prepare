<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationLog extends Model
{
    protected $table = 'escalation_logs';

    protected $fillable = [
        'escalatable_id',
        'escalatable_type',
        'compliance_id',        // Only used by social_safeguard category
        'day_mark',
        'level',
        'type',
        'escalation_category',  // social_safeguard | physical_progress | financial_progress | contract_security
    ];

    // ─────────────────────────────────────────────
    // Category Constants — use these everywhere
    // ─────────────────────────────────────────────
    const CATEGORY_SOCIAL      = 'social_safeguard';
    const CATEGORY_PHYSICAL    = 'physical_progress';
    const CATEGORY_FINANCIAL   = 'financial_progress';
    const CATEGORY_SECURITY    = 'contract_security';

    /** Human-readable labels for display in views */
    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_SOCIAL    => 'Social Safeguard',
            self::CATEGORY_PHYSICAL  => 'Physical Progress',
            self::CATEGORY_FINANCIAL => 'Financial Progress',
            self::CATEGORY_SECURITY  => 'Contract Security',
        ];
    }

    /** Badge colours (Bootstrap) for each category */
    public static function categoryColors(): array
    {
        return [
            self::CATEGORY_SOCIAL    => 'primary',
            self::CATEGORY_PHYSICAL  => 'warning',
            self::CATEGORY_FINANCIAL => 'info',
            self::CATEGORY_SECURITY  => 'danger',
        ];
    }

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Polymorphic target — SubPackageProject, ContractSecurity, Contract, etc.
     */
    public function escalatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Safeguard compliance — only relevant for social_safeguard category.
     */
    public function compliance(): BelongsTo
    {
        return $this->belongsTo(SafeguardCompliance::class, 'compliance_id');
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->escalation_category] ?? ucfirst($this->escalation_category);
    }

    public function getCategoryColorAttribute(): string
    {
        return self::categoryColors()[$this->escalation_category] ?? 'secondary';
    }
}