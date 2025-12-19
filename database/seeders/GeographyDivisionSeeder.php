<?php

namespace Database\Seeders;

// database/seeders/GeographyDivisionSeeder.php
use App\Models\GeographyDivision;
use Illuminate\Database\Seeder;

class GeographyDivisionSeeder extends Seeder
{
    public function run()
    {
        $divisions = [
            ['id' => 1, 'name' => 'Garhwal', 'slug' => 'garhwal'],
            ['id' => 2, 'name' => 'Kumaon', 'slug' => 'kumaon'],
        ];

        foreach ($divisions as $division) {
            GeographyDivision::updateOrCreate(
                ['id' => $division['id']],
                $division
            );
        }
    }
}