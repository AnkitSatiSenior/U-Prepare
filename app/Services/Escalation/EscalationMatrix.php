<?php

namespace App\Services\Escalation;

class EscalationMatrix
{
    /**
     * Maps the elapsed days to an array of specific actions per hierarchy level.
     * Type is 'alert' for the first contact, 'reminder' for subsequent contacts.
     */
    public static function getRules(): array
    {
        return [
            1  => [
                ['level' => 1, 'type' => 'alert',    'count_so_far' => 1],
            ],
            7  => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 2],
            ],
            14 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 3],
            ],
            17 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 2, 'type' => 'alert',    'count_so_far' => 1], // First time for L2
            ],
            20 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 2],
            ],
            23 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 6],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 3, 'type' => 'alert',    'count_so_far' => 1], // First time for L3
            ],
            26 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 7],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 4],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 4, 'type' => 'alert',    'count_so_far' => 1], // First time for L4
            ],
            28 => [
                ['level' => 1, 'type' => 'reminder', 'count_so_far' => 8],
                ['level' => 2, 'type' => 'reminder', 'count_so_far' => 5],
                ['level' => 3, 'type' => 'reminder', 'count_so_far' => 3],
                ['level' => 4, 'type' => 'reminder', 'count_so_far' => 2],
                ['level' => 5, 'type' => 'alert',    'count_so_far' => 1], // First time for L5
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
}