<?php

namespace App\Services\Escalation;

use App\Models\EscalationLog;

/**
 * ESCALATION MATRIX — MULTI-CATEGORY
 *
 * Defines the day-threshold → action rules for each escalation category.
 * Each entry maps: int $daysViolated => array of actions (level + type).
 */
class EscalationMatrix
{
    /**
     * SOCIAL SAFEGUARD RULES
     * Violation: Pre-Construction phase incomplete while During Construction has started.
     */
    public static function getSocialRules(): array
    {
        return [
            1  => [['level' => 1, 'type' => 'alert',    'count_so_far' => 1]],
            7  => [['level' => 1, 'type' => 'reminder', 'count_so_far' => 2]],
            14 => [['level' => 1, 'type' => 'reminder', 'count_so_far' => 3]],
            17 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 2, 'type' => 'alert',    'count_so_far' => 1],
            ],
            20 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 2],
            ],
            23 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 3, 'type' => 'alert',    'count_so_far' => 1],
            ],
            26 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 7],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 4, 'type' => 'alert',    'count_so_far' => 1],
            ],
            28 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 8],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 5, 'type' => 'alert',    'count_so_far' => 1],
            ],
            30 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 9],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 5, 'type' => 'reminder', 'count_so_far' => 2],
            ],
        ];
    }

    /**
     * PHYSICAL PROGRESS RULES
     * Violation: Contract started but no BOQ/EPC physical progress submitted in N days.
     */
    public static function getPhysicalRules(): array
    {
        return [
            7  => [['level' => 1, 'type' => 'alert',    'count_so_far' => 1]],
            14 => [['level' => 1, 'type' => 'reminder', 'count_so_far' => 2]],
            21 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 2, 'type' => 'alert',    'count_so_far' => 1],
            ],
            30 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 3, 'type' => 'alert',    'count_so_far' => 1],
            ],
            45 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 4, 'type' => 'alert',    'count_so_far' => 1],
            ],
            60 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 5, 'type' => 'alert',    'count_so_far' => 1],
            ],
        ];
    }

    /**
     * FINANCIAL PROGRESS RULES
     * Violation: No financial bill submitted since commencement or last submission.
     */
    public static function getFinancialRules(): array
    {
        return [
            30  => [['level' => 1, 'type' => 'alert',    'count_so_far' => 1]],
            45  => [['level' => 1, 'type' => 'reminder', 'count_so_far' => 2]],
            60  => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 2, 'type' => 'alert',    'count_so_far' => 1],
            ],
            75  => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 3, 'type' => 'alert',    'count_so_far' => 1],
            ],
            90  => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 4, 'type' => 'alert',    'count_so_far' => 1],
            ],
            120 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 5, 'type' => 'alert',    'count_so_far' => 1],
            ],
        ];
    }

    /**
     * CONTRACT SECURITY RULES
     * Violation: Security near expiry or already expired.
     * Days Violated = days elapsed since the 30-day warning window started.
     *   Example: 25 days remaining  → days_violated = 5
     *   Example: expired 10 days ago → days_violated = 40
     */
    public static function getSecurityRules(): array
    {
        return [
            1  => [['level' => 1, 'type' => 'alert',    'count_so_far' => 1]],
            7  => [['level' => 1, 'type' => 'reminder', 'count_so_far' => 2]],
            15 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 2, 'type' => 'alert',    'count_so_far' => 1],
            ],
            22 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 3, 'type' => 'alert',    'count_so_far' => 1],
            ],
            30 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 4, 'type' => 'alert',    'count_so_far' => 1],
            ],
            37 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 5, 'type' => 'alert',    'count_so_far' => 1],
            ],
        ];
    }

    /** Backward-compatible alias — keeps existing social escalation working */
    public static function getRules(): array
    {
        return self::getSocialRules();
    }

    /** Get ruleset by category name */
    public static function getRulesFor(string $category): array
    {
        return match ($category) {
            EscalationLog::CATEGORY_SOCIAL    => self::getSocialRules(),
            EscalationLog::CATEGORY_PHYSICAL  => self::getPhysicalRules(),
            EscalationLog::CATEGORY_FINANCIAL => self::getFinancialRules(),
            EscalationLog::CATEGORY_SECURITY  => self::getSecurityRules(),
            default                           => [],
        };
    }

    /** Find the highest day-mark whose threshold the daysViolated has crossed */
    public static function findApplicableDayMark(array $rules, int $daysViolated): ?int
    {
        $sortedDays = array_keys($rules);
        rsort($sortedDays);

        foreach ($sortedDays as $day) {
            if ($daysViolated >= $day) {
                return $day;
            }
        }

        return null;
    }
}