<?php

namespace Database\Seeders;

use App\Domain\User\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $phone = '1111111111';

        DB::table('app_users')->insert([
            'first_name' => 'ادمن',
            'last_name' => 'النظام',
            'id_card' => null,
            'phone' => $phone,
            'user_name' => $phone,
            'image' => null,
            'password' => Hash::make('12345678'),
            'must_change_password' => false,
            'role' => UserRole::Admin->value,
            'department_id' => 1,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
