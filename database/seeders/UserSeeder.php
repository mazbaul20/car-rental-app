<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                "name" => "Admin",
                'email' => "admin@gmail.com",
                "role" => "admin",
                "password" => Hash::make("111")
            ],
            //customer
            [
                "name" => "Customer",
                'email' => "customer@gmail.com",
                "role" => "customer",
                "password" => Hash::make("111")
            ]
        ]);
        User::factory()->count(10)->create();

    }
}
