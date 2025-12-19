<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstituencySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('constituencies')->insert([
            [
                'id' => 1,
                'name' => 'Tehri Garhwal',
                'status' => 1,
                'created_at' => '2024-03-27 06:47:45',
                'updated_at' => '2024-03-27 06:47:45',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Garhwal',
                'status' => 1,
                'created_at' => '2024-03-27 06:48:58',
                'updated_at' => '2024-03-27 06:48:58',
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'name' => 'Haridwar',
                'status' => 1,
                'created_at' => '2024-03-27 06:48:58',
                'updated_at' => '2024-03-27 06:48:58',
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Almora',
                'status' => 1,
                'created_at' => '2024-03-27 07:28:41',
                'updated_at' => '2024-03-27 07:28:41',
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'name' => 'Nainital–Udhamsingh Nagar',
                'status' => 1,
                'created_at' => '2024-03-27 07:28:59',
                'updated_at' => '2024-03-27 07:28:59',
                'deleted_at' => null,
            ],
        ]);
    }
}
