<?php

namespace Database\Seeders;

// database/seeders/GeographyBlockSeeder.php
use App\Models\GeographyBlock;
use Illuminate\Database\Seeder;

class GeographyBlockSeeder extends Seeder
{
    public function run()
    {
        $blocks = [
            ['id' => 1, 'division_id' => 1, 'district_id' => 10, 'name' => 'Agastmuni', 'slug' => 'agastmuni'],
            ['id' => 2, 'division_id' => 2, 'district_id' => 2, 'name' => 'Bageshwar', 'slug' => 'bageshwar'],
            ['id' => 3, 'division_id' => 1, 'district_id' => 6, 'name' => 'Bahadrabad', 'slug' => 'bahadrabad'],
            // ... add all 95 blocks in this format
            ['id' => 95, 'division_id' => 1, 'district_id' => 8, 'name' => 'Yamkeshwar', 'slug' => 'yamkeshwar'],
        ];

        foreach ($blocks as $block) {
            GeographyBlock::updateOrCreate(
                ['id' => $block['id']],
                $block
            );
        }
    }
}