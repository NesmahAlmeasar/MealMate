<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NutritionistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * هذا الـ Seeder يقوم بإضافة سجلات للأخصائيين في جدول nutritionists
     * بناءً على المستخدمين الموجودين في جدول user_roles بدور specialist
     */
    public function run(): void
    {
        echo "🔄 بدء إضافة الأخصائيين إلى جدول nutritionists...\n\n";

        // الحصول على جميع المستخدمين الذين لديهم دور specialist
        $specialists = DB::table('users')
            ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('roles.role_name', 'specialist')
            ->select('users.user_id', 'users.Fname', 'users.Lname')
            ->get();

        if ($specialists->isEmpty()) {
            echo "⚠️  لا يوجد مستخدمين بدور specialist في النظام!\n";

            return;
        }

        echo '📊 عدد الأخصائيين الموجودين: '.$specialists->count()."\n\n";

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($specialists as $specialist) {
            // التحقق من عدم وجود السجل مسبقاً
            $exists = DB::table('nutritionists')
                ->where('nutritionist_id', $specialist->user_id)
                ->exists();

            if ($exists) {
                echo "   ⏭️  تم تخطي: {$specialist->Fname} {$specialist->Lname} (موجود مسبقاً)\n";
                $skippedCount++;

                continue;
            }

            // إضافة الأخصائي إلى جدول nutritionists
            DB::table('nutritionists')->insert([
                'nutritionist_id' => $specialist->user_id,
                'Academic_level' => 'بكالوريوس تغذية', // قيمة افتراضية
                'description' => 'أخصائي تغذية معتمد', // قيمة افتراضية
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "   ✅ تمت إضافة: {$specialist->Fname} {$specialist->Lname} (ID: {$specialist->user_id})\n";
            $addedCount++;
        }

        echo "\n✨ تم الانتهاء من إضافة الأخصائيين!\n";
        echo "📊 الإحصائيات:\n";
        echo "   - تمت إضافة: {$addedCount}\n";
        echo "   - تم تخطي: {$skippedCount}\n";
        echo '   - الإجمالي: '.($addedCount + $skippedCount)."\n";
    }
}
