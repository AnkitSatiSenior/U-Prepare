<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectsCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('projects_category')->insert([
            [
                'id' => 1,
                'name' => 'Works',
                'methods_of_procurement' => json_encode([
                    "Request for Proposals",
                    "Request for Bids",
                    "Request for Quotations",
                    "Direct Selection"
                ]),
                'status' => '1',
                'created_at' => Carbon::parse('2024-03-01 06:09:38'),
                'updated_at' => Carbon::parse('2024-03-05 06:10:22'),
            ],
            [
                'id' => 2,
                'name' => 'Consultancy Services',
                'methods_of_procurement' => json_encode([
                    "Quality and Cost-Budget Selection",
                    "Fixed Budget Selection",
                    "Least Cost Selection",
                    "Quality Based Selection",
                    "Consultant Qualification Selection",
                    "Direct Selection",
                    "Individual Consultant Selection",
                    "Request for perposal",
                    "EOI"
                ]),
                'status' => '1',
                'created_at' => Carbon::parse('2024-03-02 06:10:03'),
                'updated_at' => Carbon::parse('2024-03-06 06:10:25'),
            ],
            [
                'id' => 3,
                'name' => 'Goods',
                'methods_of_procurement' => json_encode([
                    "Request for Proposals",
                    "Request for Bids",
                    "Request for Quotations"
                ]),
                'status' => '1',
                'created_at' => Carbon::parse('2024-03-03 06:10:12'),
                'updated_at' => Carbon::parse('2024-03-06 06:10:28'),
            ],
            [
                'id' => 4,
                'name' => 'Others',
                'methods_of_procurement' => json_encode([
                    "Request for Proposals",
                    "Request for Bids",
                    "Request for Quotations",
                    "Direct Selection"
                ]),
                'status' => '1',
                'created_at' => Carbon::parse('2024-03-04 06:10:17'),
                'updated_at' => Carbon::parse('2024-03-06 06:10:40'),
            ],
        ]);
    }
}
