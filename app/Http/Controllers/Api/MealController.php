<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealController extends Controller
{
    /**
     * API 7: عرض تفاصيل الوجبة
     * GET /api/meals/{mealId}
     */
    public function show($mealId): JsonResponse
    {
        try {
            $meal = Meal::with(['category', 'restaurant', 'ingredients', 'diets'])
                ->where('meals_id', $mealId)
                ->where('state', 'approved')
                ->first();

            if (! $meal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الوجبة غير موجودة أو غير معتمدة',
                ], 404);
            }

            // تحضير المعلومات الغذائية
            $nutritionalInfo = [
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'fat_g' => $meal->fat_g,
                'carbs_g' => $meal->carbs_g,
                'quantity_g' => $meal->quantity_g,
            ];

            // تحضير المكونات
            $ingredients = $meal->ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->ingredients_id,
                    'name_ar' => $ingredient->name_ar,
                    'quantity_g' => $ingredient->pivot->quantity_g ?? null,
                    'calories' => $ingredient->calories,
                    'protein_g' => $ingredient->protein_g,
                    'fat_g' => $ingredient->fat_g,
                    'carbs_g' => $ingredient->carbs_g,
                    'is_vegan' => (bool) $ingredient->is_vegan,
                    'has_gluten' => (bool) $ingredient->has_gluten,
                    'has_dairy' => (bool) $ingredient->has_dairy,
                ];
            });

            // تحضير الحميات المتوافقة
            $compatibleDiets = $meal->diets->map(function ($diet) {
                return [
                    'id' => $diet->diets_id,
                    'name' => $diet->name,
                ];
            });

            $formattedMeal = [
                'id' => $meal->meals_id,
                'name' => $meal->name,
                'description' => $meal->description,
                'photo_url' => $meal->photo_url,
                'price' => $meal->price,
                'state' => $meal->state,

                'preparation_time' => $meal->preparation_time,
                'quantity_g' => $meal->quantity_g,
                'category' => $meal->category ? [
                    'id' => $meal->category->category_id,
                    'name' => $meal->category->category_name,
                ] : null,
                'restaurant' => $meal->restaurant ? [
                    'id' => $meal->restaurant->restaurants_id,
                    'name' => $meal->restaurant->name,
                ] : null,
                'nutritional_info' => $nutritionalInfo,
                'ingredients' => $ingredients,
                'compatible_diets' => $compatibleDiets,
                'created_at' => $meal->created_at,
                'updated_at' => $meal->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب تفاصيل الوجبة بنجاح',
                'data' => $formattedMeal,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 8: وصف تفصيلي للوجبة
     * GET /api/meals/{mealId}/description
     */
    public function getDescription($mealId): JsonResponse
    {
        try {
            $meal = Meal::where('meals_id', $mealId)
                ->where('state', 'approved')
                ->select([
                    'meals_id as id',
                    'name',
                    'description',
                    'photo_url',

                    'preparation_time',
                ])
                ->first();

            if (! $meal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الوجبة غير موجودة أو غير معتمدة',
                ], 404);
            }

            // الحصول على المعلومات الغذائية الأساسية
            $basicNutrition = Meal::where('meals_id', $mealId)
                ->select(['calories', 'protein_g', 'fat_g', 'carbs_g', 'quantity_g'])
                ->first();

            $formattedDescription = [
                'id' => $meal->id,
                'name' => $meal->name,
                'description' => $meal->description,
                'photo_url' => $meal->photo_url,

                'preparation_time' => $meal->preparation_time.' دقيقة',
                'basic_nutrition' => [
                    'calories' => $basicNutrition->calories,
                    'protein' => $basicNutrition->protein_g.' جرام',
                    'fat' => $basicNutrition->fat_g.' جرام',
                    'carbs' => $basicNutrition->carbs_g.' جرام',
                    'total_weight' => $basicNutrition->quantity_g.' جرام',
                ],
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب وصف الوجبة بنجاح',
                'data' => $formattedDescription,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 9: البحث عن وجبات
     * GET /api/meals/search
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Meal::with(['category', 'restaurant'])
                ->where('state', 'approved');

            // فلتر حسب النص
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // فلتر حسب المطعم
            if ($request->has('restaurant_id')) {
                $query->where('restaurant_id', $request->restaurant_id);
            }

            // فلتر حسب الفئة
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // فلتر حسب الحمية
            if ($request->has('diet_id')) {
                $query->whereHas('diets', function ($q) use ($request) {
                    $q->where('diet_meals.diets_id', $request->diet_id);
                });
            }

            // فلتر حسب السعر
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // تحديد الحقول المطلوبة
            $meals = $query->select([
                'meals_id as id',
                'name',
                'description',
                'photo_url',
                'price',
                'calories',
                'protein_g',
                'fat_g',
                'carbs_g',

                'quantity_g',
                'restaurant_id',
                'category_id',
            ])->paginate($request->per_page ?? 15);

            return response()->json([
                'status' => 'success',
                'message' => 'تم البحث بنجاح',
                'data' => [
                    'meals' => $meals->items(),
                    'pagination' => [
                        'current_page' => $meals->currentPage(),
                        'last_page' => $meals->lastPage(),
                        'per_page' => $meals->perPage(),
                        'total' => $meals->total(),
                    ],
                    'filters_applied' => $request->all(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في البحث',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 12: عرض تفاصيل وجبة محددة بجميع السمات
     * GET /api/meals/{id}/full-details
     */
    public function getFullDetails($mealId): JsonResponse
    {
        try {
            $meal = Meal::with([
                'category',
                'restaurant',
                'ingredients',
                'diets',
            ])->where('meals_id', $mealId)
                ->where('state', 'approved')
                ->first();

            if (! $meal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الوجبة غير موجودة أو غير معتمدة',
                ], 404);
            }

            $response = [
                'id' => $meal->meals_id,
                'name' => $meal->name,
                'description' => $meal->description,
                'photo_url' => $meal->photo_url,
                'price' => (float) $meal->price,
                'state' => $meal->state,

                'preparation_time' => $meal->preparation_time,
                'quantity_g' => (float) $meal->quantity_g,
                'calories' => (float) $meal->calories,
                'protein_g' => (float) $meal->protein_g,
                'fat_g' => (float) $meal->fat_g,
                'carbs_g' => (float) $meal->carbs_g,
                'category' => $meal->category ? [
                    'id' => $meal->category->category_id,
                    'name' => $meal->category->category_name,
                ] : null,
                'restaurant' => $meal->restaurant ? [
                    'id' => $meal->restaurant->restaurants_id,
                    'name' => $meal->restaurant->name,
                    'photo_url' => $meal->restaurant->photo_url,
                ] : null,
                'ingredients' => $meal->ingredients->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->ingredients_id,
                        'name_ar' => $ingredient->name_ar,
                        'usda_term' => $ingredient->usda_term,
                        'is_vegan' => (bool) $ingredient->is_vegan,
                        'has_gluten' => (bool) $ingredient->has_gluten,
                        'has_dairy' => (bool) $ingredient->has_dairy,
                        'quantity_g' => $ingredient->pivot->quantity_g ?? null,
                        'calories' => (float) $ingredient->calories,
                        'protein_g' => (float) $ingredient->protein_g,
                        'fat_g' => (float) $ingredient->fat_g,
                        'carbs_g' => (float) $ingredient->carbs_g,
                    ];
                }),
                'diets' => $meal->diets->map(function ($diet) {
                    return [
                        'id' => $diet->diets_id,
                        'name' => $diet->name,
                        'photo_url' => $diet->photo_url,
                    ];
                }),
                'nutritional_summary' => [
                    'calories_per_100g' => $meal->quantity_g > 0 ?
                        round(($meal->calories / $meal->quantity_g) * 100, 2) : 0,
                    'protein_percentage' => $meal->calories > 0 ?
                        round((($meal->protein_g * 4) / $meal->calories) * 100, 2) : 0,
                    'fat_percentage' => $meal->calories > 0 ?
                        round((($meal->fat_g * 9) / $meal->calories) * 100, 2) : 0,
                    'carbs_percentage' => $meal->calories > 0 ?
                        round((($meal->carbs_g * 4) / $meal->calories) * 100, 2) : 0,
                ],
                'created_at' => $meal->created_at,
                'updated_at' => $meal->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب تفاصيل الوجبة بنجاح',
                'data' => $response,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
