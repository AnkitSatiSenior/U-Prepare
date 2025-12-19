<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('districts')->insert([
            ['id' => 1, 'name' => 'Almora', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Bageshwar', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Chamoli', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Champawat', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Dehradun', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Haridwar', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Nainital', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Pauri Garhwal', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Pithoragarh', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Rudraprayag', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Tehri Garhwal', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Udham Singh Nagar', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Uttarkashi', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'All', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
