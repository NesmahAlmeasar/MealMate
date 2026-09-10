<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Nutrition Manager',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Specialist',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'User',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // حذف الأدوار الموجودة وإعادة إدراجها
        DB::table('roles')->truncate();
        DB::table('roles')->insert($roles);

        $this->command->info('✅ تم إضافة 4 أدوار بنجاح!');
    }
}
