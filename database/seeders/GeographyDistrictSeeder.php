<?php

namespace Database\Seeders;

// database/seeders/GeographyDistrictSeeder.php
use App\Models\GeographyDistrict;
use Illuminate\Database\Seeder;

class GeographyDistrictSeeder extends Seeder
{
    public function run()
    {
        $districts = [
            ['id' => 1, 'division_id' => 2, 'name' => 'Almora', 'slug' => 'almora'],
            ['id' => 2, 'division_id' => 2, 'name' => 'Bageshwar', 'slug' => 'bageshwar'],
            ['id' => 3, 'division_id' => 1, 'name' => 'Chamoli', 'slug' => 'chamoli'],
            ['id' => 4, 'division_id' => 2, 'name' => 'Champawat', 'slug' => 'champawat'],
            ['id' => 5, 'division_id' => 1, 'name' => 'Dehradun', 'slug' => 'dehradun'],
            ['id' => 6, 'division_id' => 1, 'name' => 'Haridwar', 'slug' => 'haridwar'],
            ['id' => 7, 'division_id' => 2, 'name' => 'Nainital', 'slug' => 'nainital'],
            ['id' => 8, 'division_id' => 1, 'name' => 'Pauri Garhwal', 'slug' => 'pauri-garhwal'],
            ['id' => 9, 'division_id' => 2, 'name' => 'Pithoragarh', 'slug' => 'pithoragarh'],
            ['id' => 10, 'division_id' => 1, 'name' => 'Rudraprayag', 'slug' => 'rudraprayag'],
            ['id' => 11, 'division_id' => 1, 'name' => 'Tehri Garhwal', 'slug' => 'tehri-garhwal'],
            ['id' => 12, 'division_id' => 2, 'name' => 'Udham Singh Nagar', 'slug' => 'udham-singh-nagar'],
            ['id' => 13, 'division_id' => 1, 'name' => 'Uttarkashi', 'slug' => 'uttarkashi'],
        ];

        foreach ($districts as $district) {
            GeographyDistrict::updateOrCreate(
                ['id' => $district['id']],
                $district
            );
        }
    }
}