<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert(
            [
                'name' => 'القسم الرئيسي.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
