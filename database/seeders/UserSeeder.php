<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    

public function run()
{
    for ($i = 1; $i <= 30; $i++) {
        User::create([
            'role_id' => 1,
            'name' => 'Test User ' . $i,
            'email' => 'testuser' . $i . '@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password
            'remember_token' => Str::random(10),
        ]);
    }
}

}
