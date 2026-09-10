<?php

namespace Database\Seeders;

use App\Models\Diet;
use App\Models\Meal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DietMealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * هذا الـ Seeder يقوم بربط الوجبات بالحميات المناسبة بناءً على القيم الغذائية والمكونات
     */
    public function run(): void
    {
        echo "🔄 بدء ربط الوجبات بالحميات المناسبة...\n\n";

        // الحصول على جميع الحميات
        $diets = Diet::all();

        if ($diets->isEmpty()) {
            echo "⚠️  لا توجد حميات في قاعدة البيانات!\n";

            return;
        }

        // الحصول على جميع الوجبات المعتمدة
        $meals = Meal::with('ingredients')->where('state', 'approved')->get();

        if ($meals->isEmpty()) {
            echo "⚠️  لا توجد وجبات معتمدة في قاعدة البيانات!\n";

            return;
        }

        echo '📊 عدد الحميات: '.$diets->count()."\n";
        echo '📊 عدد الوجبات المعتمدة: '.$meals->count()."\n\n";

        foreach ($diets as $diet) {
            echo "🍽️  معالجة حمية: {$diet->name} (ID: {$diet->diets_id})\n";

            $suitableMeals = [];

            foreach ($meals as $meal) {
                if ($this->isMealSuitableForDiet($meal, $diet)) {
                    $suitableMeals[] = $meal->meals_id;
                }
            }

            if (! empty($suitableMeals)) {
                // ربط الوجبات المناسبة بالحمية
                $diet->meals()->syncWithoutDetaching($suitableMeals);
                echo '   ✅ تم ربط '.count($suitableMeals)." وجبة بهذه الحمية\n";
            } else {
                echo "   ⚠️  لم يتم العثور على وجبات مناسبة لهذه الحمية\n";
            }
        }

        echo "\n✨ تم الانتهاء من ربط الوجبات بالحميات بنجاح!\n";

        // عرض إحصائيات
        $totalRelations = DB::table('diet_meals')->count();
        echo "\n📊 إجمالي العلاقات في جدول diet_meals: {$totalRelations}\n";
    }

    /**
     * تحديد ما إذا كانت الوجبة مناسبة للحمية
     */
    private function isMealSuitableForDiet(Meal $meal, Diet $diet): bool
    {
        // قواعد بسيطة لتحديد مناسبة الوجبة للحمية
        // يمكن تخصيص هذه القواعد حسب احتياجاتك

        $dietName = strtolower($diet->name);

        // حمية كيتو (Keto)
        if (str_contains($dietName, 'keto')) {
            // الكيتو: قليل الكربوهيدرات، عالي الدهون
            if ($meal->carbs_g !== null && $meal->carbs_g <= 10) {
                return true;
            }
        }

        // حمية نباتية (Vegan/Vegetarian)
        if (str_contains($dietName, 'vegan') || str_contains($dietName, 'vegetarian')) {
            // التحقق من أن جميع المكونات نباتية
            $allVegan = true;
            foreach ($meal->ingredients as $ingredient) {
                if (! $ingredient->is_vegan) {
                    $allVegan = false;
                    break;
                }
            }
            if ($allVegan && $meal->ingredients->count() > 0) {
                return true;
            }
        }

        // حمية خالية من الجلوتين (Gluten-Free)
        if (str_contains($dietName, 'gluten')) {
            // التحقق من أن لا يوجد جلوتين في المكونات
            $hasGluten = false;
            foreach ($meal->ingredients as $ingredient) {
                if ($ingredient->has_gluten) {
                    $hasGluten = true;
                    break;
                }
            }
            if (! $hasGluten && $meal->ingredients->count() > 0) {
                return true;
            }
        }

        // حمية قليلة السعرات (Low-Calorie)
        if (str_contains($dietName, 'low calorie') || str_contains($dietName, 'diet')) {
            if ($meal->calories !== null && $meal->calories <= 400) {
                return true;
            }
        }

        // حمية عالية البروتين (High-Protein)
        if (str_contains($dietName, 'protein') || str_contains($dietName, 'muscle')) {
            if ($meal->protein_g !== null && $meal->protein_g >= 25) {
                return true;
            }
        }

        // حمية متوازنة (Balanced)
        if (str_contains($dietName, 'balanced') || str_contains($dietName, 'healthy')) {
            // وجبة متوازنة: سعرات معتدلة، نسب جيدة من البروتين والكربوهيدرات
            if ($meal->calories !== null &&
                $meal->calories >= 300 &&
                $meal->calories <= 600 &&
                $meal->protein_g !== null &&
                $meal->protein_g >= 15) {
                return true;
            }
        }

        return false;
    }
}
