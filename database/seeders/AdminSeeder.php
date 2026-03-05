<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('app_users')->insert([
            'first_name' => 'Abdullah',
            'last_name' => 'Bawazir',
            'date_of_birth' => '1990-01-01',
            'id_card' => null,
            'user_name' => 'AbdullahBawazir',
            'phone' => '777777777',
            'password' => Hash::make('11223344'),
            'must_change_password' => false,
            'role' => 'Admin',
            'department_id' => 1,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}