<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sub_category')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Others',
                'status' => 1,
                'created_at' => '2024-03-28 04:46:05',
                'updated_at' => '2024-03-28 04:46:05',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'name' => 'Others',
                'status' => 1,
                'created_at' => '2024-03-28 04:46:21',
                'updated_at' => '2024-03-28 04:46:21',
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'name' => 'Others',
                'status' => 1,
                'created_at' => '2024-03-28 04:46:43',
                'updated_at' => '2024-03-28 04:46:43',
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'category_id' => 4,
                'name' => 'Others',
                'status' => 1,
                'created_at' => '2024-03-28 04:47:13',
                'updated_at' => '2024-03-28 04:48:33',
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'category_id' => 1,
                'name' => 'Bridge',
                'status' => 1,
                'created_at' => '2024-03-30 12:37:04',
                'updated_at' => '2024-04-01 10:19:56',
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'category_id' => 1,
                'name' => 'Building',
                'status' => 1,
                'created_at' => '2024-03-30 12:37:17',
                'updated_at' => '2024-04-01 10:24:56',
                'deleted_at' => null,
            ],
            [
                'id' => 7,
                'category_id' => 1,
                'name' => 'Slope Protection',
                'status' => 1,
                'created_at' => '2024-03-30 12:37:26',
                'updated_at' => '2024-07-05 10:08:14',
                'deleted_at' => null,
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'name' => 'Tent',
                'status' => 1,
                'created_at' => '2024-03-30 12:38:01',
                'updated_at' => '2024-03-30 12:38:01',
                'deleted_at' => null,
            ],
            [
                'id' => 9,
                'category_id' => 3,
                'name' => 'Shelter',
                'status' => 1,
                'created_at' => '2024-03-30 12:38:07',
                'updated_at' => '2024-03-30 12:38:07',
                'deleted_at' => null,
            ],
            [
                'id' => 10,
                'category_id' => 3,
                'name' => 'Fire-suit',
                'status' => 1,
                'created_at' => '2024-03-30 12:38:28',
                'updated_at' => '2024-03-30 12:38:28',
                'deleted_at' => null,
            ],
            [
                'id' => 11,
                'category_id' => 2,
                'name' => 'Supervision Design',
                'status' => 1,
                'created_at' => '2024-03-30 12:38:48',
                'updated_at' => '2024-07-05 08:52:00',
                'deleted_at' => null,
            ],
            [
                'id' => 12,
                'category_id' => 2,
                'name' => 'Supervision',
                'status' => 1,
                'created_at' => '2024-03-30 12:38:58',
                'updated_at' => '2024-07-05 08:52:14',
                'deleted_at' => null,
            ],
            [
                'id' => 13,
                'category_id' => 2,
                'name' => 'Design',
                'status' => 1,
                'created_at' => '2024-07-05 08:50:51',
                'updated_at' => '2024-07-05 08:52:22',
                'deleted_at' => null,
            ],
        ]);
    }
}
