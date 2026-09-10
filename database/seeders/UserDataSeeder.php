<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles
        $roles = [
            [
                'role_id' => 1,
                'name' => 'Admin',
                'description' => 'مسؤول النظام، لديه صلاحيات كاملة لإدارة المستخدمين والمطاعم والتقارير.',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'role_id' => 2,
                'name' => 'Specialist',
                'description' => 'أخصائي تغذية، لديه صلاحيات إدارة الحميات والأطباق والتواصل مع العملاء.',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'role_id' => 3,
                'name' => 'Client',
                'description' => 'مستخدم عادي، لديه صلاحيات تصفح الحميات وطلب الاستشارات.',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];

        // Using insertOrIgnore to avoid duplicates if re-run
        DB::table('roles')->insertOrIgnore($roles);

        // 2. Users
        // Note: Setting all passwords to '12345678' so you can login.
        $password = Hash::make('12345678');
        $now = Carbon::now();

        $users = [
            [
                'user_id' => 4,
                'phone' => '7157715',
                'Fname' => 'نسمة',
                'Lname' => 'المعصار',
                'email' => 'na@gmail.com',
                'photo_url' => null,
                'account_state' => 'Active',
                'password' => $password,
                'created_at' => '2025-11-21 18:52:22',
                'updated_at' => '2025-11-21 18:52:22',
            ],
            [
                'user_id' => 5,
                'phone' => '7157',
                'Fname' => 'نورا',
                'Lname' => 'المعصار',
                'email' => 'n@gmail.com',
                'photo_url' => 'profile_photos/uz4kX6GFs5Nvx2GbKRvzAkPjPxxprXwdIKu4LxmL.png',
                'account_state' => 'Active',
                'password' => $password,
                'created_at' => '2025-11-21 18:53:21',
                'updated_at' => '2025-11-21 18:53:21',
            ],
            [
                'user_id' => 6,
                'phone' => '715722',
                'Fname' => 'abdullah',
                'Lname' => 'المختار',
                'email' => 'abdullah@gmail.com',
                'photo_url' => 'profile_photos/ZIMC5zWfH6Zep7r4Gabh3DYCvg2gh9OClQ58NVDK.png',
                'account_state' => 'Active',
                'password' => $password,
                'created_at' => '2025-11-23 05:49:55',
                'updated_at' => '2025-11-23 05:50:22',
            ],
            [
                'user_id' => 7,
                'phone' => '7157111',
                'Fname' => 'abdullah',
                'Lname' => 'المعصار',
                'email' => 'abd@gmail.com',
                'photo_url' => 'profile_photos/UhJ1ntOr1HlFEjyx8K1iKXGIiANcFkrwxnsmXLPs.png',
                'account_state' => 'Active',
                'password' => $password,
                'created_at' => '2025-11-23 18:55:18',
                'updated_at' => '2025-11-23 18:55:18',
            ],
        ];

        DB::table('users')->insertOrIgnore($users);

        // 3. User Roles
        $userRoles = [
            ['user_id' => 4, 'role_id' => 1],
            ['user_id' => 4, 'role_id' => 2],
            ['user_id' => 5, 'role_id' => 3],
            ['user_id' => 6, 'role_id' => 1],
            ['user_id' => 7, 'role_id' => 2],
        ];

        DB::table('user_roles')->insertOrIgnore($userRoles);

        $this->command->info('Legacy users and roles seeded successfully! Password for all is: 12345678');
    }
}
