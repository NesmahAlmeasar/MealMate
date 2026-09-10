<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Diet;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * API 1: عرض جميع المطاعم
     * GET /api/restaurants
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $restaurants = Restaurant::withCount(['meals' => function ($query) {
                $query->where('state', 'approved');
            }])
            ->orderBy('meals_count', 'desc')
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->restaurants_id,
                    'name' => $restaurant->name,
                    'description' => $restaurant->description,
                    'email' => $restaurant->email,
                    'photo_url' => $restaurant->photo_url,
                    'state' => $restaurant->state,
                    'meals_count' => $restaurant->meals_count,
                    'created_at' => $restaurant->created_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب المطاعم بنجاح',
                'data' => $restaurants,
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
     * API 2: عرض الأقسام (الفئات) داخل مطعم معين
     * GET /api/restaurants/{restaurantId}/categories
     */
    public function getCategories($restaurantId): JsonResponse
    {
        try {
            // البحث عن المطعم
            $restaurant = Restaurant::find($restaurantId);

            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            // الحصول على فئات المطعم بناءً على الوجبات المتوفرة فيه
            // بدلاً من استخدام جدول العلاقة المباشرة، نبحث عن الأقسام التي لديها وجبات في هذا المطعم
            $categories = Category::whereHas('meals', function ($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId)
                    ->where('state', 'approved'); // اختياري: جلب الأقسام التي تحتوي على وجبات معتمدة فقط
            })
                ->select('category.category_id', 'category.category_name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الأقسام بنجاح',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                        'photo_url' => $restaurant->photo_url,
                    ],
                    'categories' => $categories,
                ],
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
     * API 3: عرض الوجبات داخل قسم معين مع مراعاة الحمية
     * GET /api/restaurants/{restaurantId}/categories/{categoryId}/meals
     */
    public function getCategoryMeals(Request $request, $restaurantId, $categoryId): JsonResponse
    {
        try {
            // التحقق من المطعم
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            // التحقق من الفئة
            $category = Category::find($categoryId);
            if (! $category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود',
                ], 404);
            }

            // التحقق من أن الفئة تابعة للمطعم (عن طريق وجود وجبات)
            $isCategoryInRestaurant = Meal::where('restaurant_id', $restaurantId)
                ->where('category_id', $categoryId)
                ->exists();

            if (! $isCategoryInRestaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذا القسم غير متوفر في المطعم المحدد',
                ], 400);
            }

            // جلب diet_id من request
            $dietId = $request->query('diet_id');

            // بناء الاستعلام
            $query = Meal::where('category_id', $categoryId)
                ->whereHas('restaurant', function ($q) use ($restaurantId) {
                    $q->where('restaurants_id', $restaurantId);
                })
                ->where('state', 'approved') // فقط الوجبات المعتمدة
                ->select([
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
                    'preparation_time',
                ]);

            // تطبيق فلتر الحمية إذا تم تحديدها
            if ($dietId) {
                $query->whereHas('diets', function ($q) use ($dietId) {
                    $q->where('diet_meals.diets_id', $dietId);
                });
            }

            $meals = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الوجبات بنجاح',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                    ],
                    'category' => [
                        'id' => $category->category_id,
                        'name' => $category->category_name,
                    ],
                    'filters_applied' => [
                        'diet_id' => $dietId,
                    ],
                    'meals' => $meals,
                    'meals_count' => $meals->count(),
                ],
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
     * API 4: عرض جميع وجبات المطعم مع إمكانية التصفية
     * GET /api/restaurants/{id}/meals
     */
    public function meals(Request $request, $id): JsonResponse
    {
        try {
            $restaurant = Restaurant::find($id);

            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            // بناء الاستعلام
            $query = Meal::whereHas('restaurant', function ($q) use ($id) {
                $q->where('restaurants_id', $id);
            });

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

            // فلتر حسب الحالة
            if ($request->has('state')) {
                $query->where('state', $request->state);
            } else {
                $query->where('state', 'approved'); // الافتراضي: المعتمدة فقط
            }

            // تحديد الحقول المطلوبة
            $meals = $query->select([
                'meals_id as id',
                'name',
                'description',
                'photo_url',
                'price',
                'state',
                'quantity_g',
                'calories',
                'protein_g',
                'fat_g',
                'carbs_g',
                'category_id',
                'preparation_time',
            ])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الوجبات بنجاح',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                    ],
                    'filters_applied' => $request->only(['category_id', 'diet_id', 'state']),
                    'meals' => $meals,
                    'meals_count' => $meals->count(),
                ],
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
     * API 5: عرض تفاصيل مطعم معين
     * GET /api/restaurants/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $restaurant = Restaurant::with(['categories', 'phones', 'location'])
                ->find($id);

            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            $formattedData = [
                'id' => $restaurant->restaurants_id,
                'name' => $restaurant->name,
                'description' => $restaurant->description,
                'email' => $restaurant->email,
                'photo_url' => $restaurant->photo_url,
                'state' => $restaurant->state,
                'categories' => $restaurant->categories->map(function ($category) {
                    return [
                        'id' => $category->category_id,
                        'name' => $category->category_name,
                    ];
                }),
                'phones' => $restaurant->phones->map(function ($phone) {
                    return $phone->phone_number;
                }),
                'location' => $restaurant->location ? [
                    'id' => $restaurant->location->location_id,
                    'latitude' => $restaurant->location->latitude_x,
                    'longitude' => $restaurant->location->longitude_y,
                    'description' => $restaurant->location->description,
                ] : null,
                'meals_count' => $restaurant->meals()->where('state', 'approved')->count(),
                'created_at' => $restaurant->created_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب تفاصيل المطعم بنجاح',
                'data' => $formattedData,
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
     * API 6: البحث عن مطاعم حسب الموقع (اختياري)
     * GET /api/restaurants/nearby
     */
    public function nearbyRestaurants(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius_km' => 'numeric|min:0.1|max:50',
            ]);

            $userLat = $request->latitude;
            $userLon = $request->longitude;
            $radius = $request->radius_km ?? 10; // نصف قطر افتراضي 10 كم

            $restaurants = Restaurant::with(['location'])
                ->whereHas('location')
                ->get()
                ->filter(function ($restaurant) use ($userLat, $userLon, $radius) {
                    if ($restaurant->location) {
                        $distance = $this->calculateDistance(
                            $userLat,
                            $userLon,
                            $restaurant->location->latitude_x,
                            $restaurant->location->longitude_y
                        );

                        $restaurant->distance_km = $distance;

                        return $distance <= $radius;
                    }

                    return false;
                })
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                        'photo_url' => $restaurant->photo_url,
                        'distance_km' => round($restaurant->distance_km, 2),
                        'meals_count' => $restaurant->meals()->where('state', 'approved')->count(),
                    ];
                })
                ->sortBy('distance_km')
                ->values();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب المطاعم القريبة بنجاح',
                'data' => [
                    'user_location' => [
                        'latitude' => $userLat,
                        'longitude' => $userLon,
                        'radius_km' => $radius,
                    ],
                    'restaurants' => $restaurants,
                    'count' => $restaurants->count(),
                ],
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
     * حساب المسافة بين إحداثيين
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * API 13: عرض الوجبات التي تناسب حمية محددة
     * GET /api/diets/{dietId}/meals
     */
    public function getMealsByDiet(Request $request, $dietId): JsonResponse
    {
        try {
            // التحقق من وجود الحمية
            $diet = Diet::find($dietId);
            if (! $diet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الحمية غير موجودة',
                ], 404);
            }

            // بناء الاستعلام
            $query = Meal::with(['category', 'restaurant'])
                ->where('state', 'approved')
                ->whereHas('diets', function ($q) use ($dietId) {
                    $q->where('diet_meals.diets_id', $dietId);
                });

            // تطبيق فلاتر إضافية
            if ($request->has('restaurant_id')) {
                $query->where('restaurant_id', $request->restaurant_id);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('min_calories')) {
                $query->where('calories', '>=', $request->min_calories);
            }

            if ($request->has('max_calories')) {
                $query->where('calories', '<=', $request->max_calories);
            }

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // ترتيب النتائج
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');

            if (in_array($sortBy, ['name', 'price', 'calories', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // التصفية حسب القيود الغذائية للحمية (إن وجدت)
            if ($diet->restrictions()->exists()) {
                foreach ($diet->restrictions as $restriction) {
                    $query->where($restriction->field_name, $restriction->operator, $restriction->value);
                }
            }

            // الحصول على النتائج
            $perPage = $request->get('per_page', 20);
            $meals = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الوجبات بنجاح',
                'data' => [
                    'diet' => [
                        'id' => $diet->diets_id,
                        'name' => $diet->name,
                        'description' => $diet->description,
                        'restrictions_count' => $diet->restrictions()->count(),
                    ],
                    'meals' => $meals->items(),
                    'pagination' => [
                        'current_page' => $meals->currentPage(),
                        'last_page' => $meals->lastPage(),
                        'per_page' => $meals->perPage(),
                        'total' => $meals->total(),
                        'from' => $meals->firstItem(),
                        'to' => $meals->lastItem(),
                    ],
                ],
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
     * API 11: عرض الوجبات في مطعم محدد وقسم محدد تناسب حمية محددة
     * GET /api/restaurants/{restaurantId}/categories/{categoryId}/diets/{dietId}/meals
     */
    public function getMealsByRestaurantCategoryAndDiet(
        Request $request,
        $restaurantId,
        $categoryId,
        $dietId
    ): JsonResponse {
        try {
            // التحقق من وجود المطعم
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            // التحقق من وجود الفئة
            $category = Category::find($categoryId);
            if (! $category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود',
                ], 404);
            }

            // التحقق من وجود الحمية
            $diet = Diet::find($dietId);
            if (! $diet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الحمية غير موجودة',
                ], 404);
            }

            // التحقق من أن الفئة تابعة للمطعم
            $isCategoryInRestaurant = Meal::where('restaurant_id', $restaurantId)
                ->where('category_id', $categoryId)
                ->exists();

            if (! $isCategoryInRestaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذا القسم غير متوفر في المطعم المحدد',
                ], 400);
            }

            // بناء الاستعلام
            $query = Meal::with(['category', 'ingredients'])
                ->where('restaurant_id', $restaurantId)
                ->where('category_id', $categoryId)
                ->where('state', 'approved')
                ->whereHas('diets', function ($q) use ($dietId) {
                    $q->where('diet_meals.diets_id', $dietId);
                });

            // تطبيق فلاتر إضافية

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // الحصول على النتائج
            $meals = $query->get([
                'meals_id',
                'name',
                'description',
                'photo_url',
                'price',
                'state',
                'preparation_time',
                'quantity_g',
                'calories',
                'protein_g',
                'fat_g',
                'carbs_g',
                'category_id',
                'restaurant_id',
                'created_at',
                'updated_at',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الوجبات بنجاح',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                    ],
                    'category' => [
                        'id' => $category->category_id,
                        'name' => $category->category_name,
                    ],
                    'diet' => [
                        'id' => $diet->diets_id,
                        'name' => $diet->name,
                    ],
                    'meals' => $meals->map(function ($meal) {
                        return [
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
                            'category_id' => $meal->category_id,
                            'restaurant_id' => $meal->restaurant_id,
                            'created_at' => $meal->created_at,
                            'updated_at' => $meal->updated_at,
                            'ingredients' => $meal->ingredients->map(function ($ingredient) {
                                return [
                                    'id' => $ingredient->ingredients_id,
                                    'name_ar' => $ingredient->name_ar,
                                    'quantity_g' => $ingredient->pivot->quantity_g ?? null,
                                ];
                            }),
                        ];
                    }),
                    'count' => $meals->count(),
                ],
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
     * API 10: عرض الوجبات في مطعم محدد تناسب حمية محددة
     * GET /api/restaurants/{restaurantId}/diets/{dietId}/meals
     */
    public function getMealsByRestaurantAndDiet(Request $request, $restaurantId, $dietId): JsonResponse
    {
        try {
            // التحقق من وجود المطعم
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المطعم غير موجود',
                ], 404);
            }

            // التحقق من وجود الحمية
            $diet = Diet::find($dietId);
            if (! $diet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الحمية غير موجودة',
                ], 404);
            }

            // بناء الاستعلام
            $query = Meal::with(['category', 'ingredients'])
                ->where('restaurant_id', $restaurantId)
                ->where('state', 'approved')
                ->whereHas('diets', function ($q) use ($dietId) {
                    $q->where('diet_meals.diets_id', $dietId);
                });

            // تطبيق فلاتر إضافية إذا وجدت

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // الحصول على النتائج
            $meals = $query->get([
                'meals_id',
                'name',
                'description',
                'photo_url',
                'price',
                'state',
                'preparation_time',
                'quantity_g',
                'calories',
                'protein_g',
                'fat_g',
                'carbs_g',
                'category_id',
                'restaurant_id',
                'created_at',
                'updated_at',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الوجبات بنجاح',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->restaurants_id,
                        'name' => $restaurant->name,
                    ],
                    'diet' => [
                        'id' => $diet->diets_id,
                        'name' => $diet->name,
                    ],
                    'meals' => $meals->map(function ($meal) {
                        return [
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
                            'restaurant_id' => $meal->restaurant_id,
                            'created_at' => $meal->created_at,
                            'updated_at' => $meal->updated_at,
                        ];
                    }),
                    'count' => $meals->count(),
                ],
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
     * API جديد: عرض المطاعم مرتبة حسب الحمية والموقع
     * GET /api/restaurants/by-diet/{dietId}
     *
     * Parameters:
     * - latitude (optional): موقع المستخدم - خط العرض
     * - longitude (optional): موقع المستخدم - خط الطول
     * - radius_km (optional): نصف القطر بالكيلومترات (default: 50)
     */
    public function getRestaurantsByDiet(Request $request, $dietId): JsonResponse
    {
        try {
            // التحقق من وجود الحمية
            $diet = Diet::find($dietId);
            if (! $diet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الحمية غير موجودة',
                ], 404);
            }

            // الحصول على موقع المستخدم إن وجد
            $userLat = $request->query('latitude');
            $userLon = $request->query('longitude');
            $radius = $request->query('radius_km', 50);

            // جلب جميع المطاعم مع عدد الوجبات المناسبة للحمية
            $restaurants = Restaurant::query()
                ->with(['location', 'phones'])
                ->withCount([
                    'meals as diet_meals_count' => function ($query) use ($dietId) {
                        $query->where('state', 'approved')
                            ->whereHas('diets', function ($q) use ($dietId) {
                                $q->where('diets.diets_id', $dietId);
                            });
                    },
                    'meals as total_meals_count' => function ($query) {
                        $query->where('state', 'approved');
                    }
                ])
                ->having('diet_meals_count', '>', 0)
                ->get();

            // إذا كان موقع المستخدم متوفراً، احسب المسافة
            if ($userLat && $userLon) {
                $restaurants = $restaurants->map(function ($restaurant) use ($userLat, $userLon) {
                    $minDistance = null;

                    // احسب أقرب مسافة من موقع المطعم
                    if ($restaurant->location) {
                        $distance = $this->calculateDistance(
                            $userLat,
                            $userLon,
                            $restaurant->location->latitude_x,
                            $restaurant->location->longitude_y
                        );
                        $minDistance = $distance;
                    }

                    $restaurant->distance_km = $minDistance;

                    return $restaurant;
                });
            }

            // ترتيب المطاعم
            if ($userLat && $userLon) {
                // إذا كان الموقع متوفراً: رتب حسب عدد الوجبات أولاً ثم المسافة
                $restaurants = $restaurants->sortBy([
                    ['diet_meals_count', 'desc'],
                    ['distance_km', 'asc'],
                ])->values();
            } else {
                // إذا لم يكن الموقع متوفراً: رتب حسب عدد الوجبات فقط
                $restaurants = $restaurants->sortByDesc('diet_meals_count')->values();
            }

            // تنسيق البيانات
            $formattedRestaurants = $restaurants->map(function ($restaurant) use ($userLat, $userLon) {
                $data = [
                    'id' => $restaurant->restaurants_id,
                    'name' => $restaurant->name,
                    'description' => $restaurant->description,
                    'photo_url' => $restaurant->photo_url,
                    'state' => $restaurant->state,
                    'diet_meals_count' => $restaurant->diet_meals_count,
                    'total_meals_count' => $restaurant->total_meals_count,
                    'phones' => $restaurant->phones->pluck('phone_number'),
                    'location' => $restaurant->location ? [
                        'id' => $restaurant->location->location_id,
                        'latitude' => $restaurant->location->latitude_x,
                        'longitude' => $restaurant->location->longitude_y,
                        'description' => $restaurant->location->description,
                    ] : null,
                ];

                // إضافة المسافة إذا كانت متوفرة
                if ($userLat && $userLon && isset($restaurant->distance_km)) {
                    $data['distance_km'] = round($restaurant->distance_km, 2);
                }

                return $data;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب المطاعم بنجاح',
                'data' => [
                    'diet' => [
                        'id' => $diet->diets_id,
                        'name' => $diet->name,
                        'description' => $diet->description,
                        'photo_url' => $diet->photo_url,
                    ],
                    'user_location' => $userLat && $userLon ? [
                        'latitude' => (float) $userLat,
                        'longitude' => (float) $userLon,
                        'radius_km' => (float) $radius,
                    ] : null,
                    'restaurants' => $formattedRestaurants,
                    'count' => $formattedRestaurants->count(),
                    'sorting' => $userLat && $userLon
                        ? 'مرتب حسب: عدد الوجبات المناسبة ثم المسافة'
                        : 'مرتب حسب: عدد الوجبات المناسبة',
                ],
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
