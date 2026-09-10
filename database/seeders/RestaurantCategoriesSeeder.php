<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * هذا الـ Seeder يقوم بربط المطاعم بالفئات بناءً على الوجبات الموجودة
     * إذا كان المطعم يحتوي على وجبات من فئة معينة، يتم ربط المطعم بهذه الفئة
     */
    public function run(): void
    {
        echo "🔄 بدء ربط المطاعم بالفئات...\n";

        // الحصول على جميع المطاعم
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            echo "\n📍 معالجة مطعم: {$restaurant->name} (ID: {$restaurant->restaurants_id})\n";

            // الحصول على جميع الفئات الموجودة في وجبات هذا المطعم
            $categoryIds = Meal::where('restaurant_id', $restaurant->restaurants_id)
                ->whereNotNull('category_id')
                ->distinct()
                ->pluck('category_id')
                ->toArray();

            if (empty($categoryIds)) {
                echo "   ⚠️  لا توجد وجبات مرتبطة بفئات لهذا المطعم\n";

                continue;
            }

            // ربط المطعم بالفئات
            $restaurant->categories()->syncWithoutDetaching($categoryIds);

            // عرض الفئات المرتبطة
            $categories = Category::whereIn('category_id', $categoryIds)->get();
            echo '   ✅ تم ربط المطعم بـ '.count($categoryIds)." فئة:\n";
            foreach ($categories as $category) {
                echo "      - {$category->category_name}\n";
            }
        }

        echo "\n✨ تم الانتهاء من ربط المطاعم بالفئات بنجاح!\n";

        // عرض إحصائيات
        $totalRelations = DB::table('restaurant_categories')->count();
        echo "\n📊 إجمالي العلاقات في جدول restaurant_categories: {$totalRelations}\n";
    }
}
