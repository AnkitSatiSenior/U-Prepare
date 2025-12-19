<?php

namespace Database\Seeders;

use App\Models\Assembly;
use Illuminate\Database\Seeder;

class AssemblySeeder extends Seeder
{
    public function run()
    {
        $assemblies = [
            ['id' => 1, 'district_id' => 13, 'constituency_id' => 1, 'name' => 'Purola', 'status' => 1],
            ['id' => 2, 'district_id' => 13, 'constituency_id' => 1, 'name' => 'Yamunotri', 'status' => 1],
            ['id' => 3, 'district_id' => 13, 'constituency_id' => 1, 'name' => 'Gangotri', 'status' => 1],
            // ... Add all 70 records in the same format
            ['id' => 70, 'district_id' => 12, 'constituency_id' => 5, 'name' => 'Khatima', 'status' => 1],
        ];

        foreach ($assemblies as $assembly) {
            Assembly::updateOrCreate(
                ['id' => $assembly['id']],
                $assembly
            );
        }
    }
}