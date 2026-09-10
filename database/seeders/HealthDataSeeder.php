<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة الأمراض المزمنة
        $chronicDiseases = [
            'السكري',
            'ارتفاع ضغط الدم',
            'الربو',
            'أمراض القلب',
            'التهاب المفاصل',
            'الصداع النصفي',
            'الكوليسترول',
            'أمراض الكلى',
            'أمراض الكبد',
            'الغدة الدرقية',
        ];

        foreach ($chronicDiseases as $disease) {
            DB::table('chronic_diseases')->updateOrInsert(
                ['chronic_diseases' => $disease],
                ['chronic_diseases' => $disease, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // إضافة أنواع الحساسية
        $allergies = [
            'البنسلين',
            'الأسبرين',
            'المكسرات',
            'القمح (الجلوتين)',
            'منتجات الألبان',
            'البيض',
            'الصويا',
            'السمك',
            'المحار',
            'الفول السوداني',
            'الغبار',
            'حبوب اللقاح',
        ];

        foreach ($allergies as $allergy) {
            DB::table('allergies')->updateOrInsert(
                ['allergies' => $allergy],
                ['allergies' => $allergy, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('تم إضافة البيانات الصحية بنجاح!');
    }
}
