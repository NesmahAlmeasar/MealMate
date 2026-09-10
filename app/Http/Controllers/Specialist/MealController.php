<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    /**
     * Display approved meals for specialist
     */
    public function index()
    {
        $meals = Meal::with(['category', 'ingredients'])
            ->where('state', 'approved')
            ->latest()
            ->get();

        return view('specialist.dishes', compact('meals'));
    }

    /**
     * Display pending meals (recently added, awaiting approval)
     */
    public function pending()
    {
        $pendingMeals = Meal::with(['category', 'ingredients'])
            ->where('state', 'pending')
            ->latest()
            ->get();

        return view('specialist.meals.pending', compact('pendingMeals'));
    }

    /**
     * Show meal details
     */
    public function show(string $id)
    {
        $meal = Meal::with(['category', 'ingredients'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'meal' => [
                'meals_id' => $meal->meals_id,
                'name' => $meal->name,
                'description' => $meal->description,
                'photo_url' => $meal->photo_url ? asset('storage/'.$meal->photo_url) : null,
                'price' => $meal->price,
                'state' => $meal->state,

                'preparation_time' => $meal->preparation_time,
                'quantity_g' => $meal->quantity_g,
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'fat_g' => $meal->fat_g,
                'carbs_g' => $meal->carbs_g,
                'category' => $meal->category ? $meal->category->category_name : null,
                'ingredients' => $meal->ingredients->map(function ($ingredient) {
                    return [
                        'ingredients_id' => $ingredient->ingredients_id,
                        'name_ar' => $ingredient->name_ar,
                        'quantity_g' => $ingredient->pivot->quantity_g,
                        'calories' => $ingredient->calories,
                        'protein_g' => $ingredient->protein_g,
                        'fat_g' => $ingredient->fat_g,
                        'carbs_g' => $ingredient->carbs_g,
                        'is_vegan' => $ingredient->is_vegan,
                        'has_gluten' => $ingredient->has_gluten,
                        'has_dairy' => $ingredient->has_dairy,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Update meal details by specialist
     */
    public function update(Request $request, string $id)
    {
        $meal = Meal::findOrFail($id);

        // Allow updating even if not pending, but primarily for pending meals correction

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',

            'preparation_time' => 'nullable|integer|min:1',
            'quantity_g' => 'nullable|numeric',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
        ]);

        $meal->update($validated);

        return response()->json(['success' => true, 'message' => 'Meal updated successfully']);
    }

    /**
     * Approve a pending meal and attach to selected diets
     */
    public function approve(Request $request, string $id)
    {
        $meal = Meal::findOrFail($id);

        if ($meal->state !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This meal is not pending approval.',
            ], 400);
        }

        // الموافقة على الوجبة
        $meal->update(['state' => 'approved']);

        // إضافة الوجبة للحميات المختارة
        if ($request->has('diet_ids') && is_array($request->diet_ids)) {
            $meal->diets()->syncWithoutDetaching($request->diet_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Meal approved and added to selected diets successfully!',
        ]);
    }

    /**
     * Reject a pending meal
     */
    public function reject(string $id)
    {
        $meal = Meal::findOrFail($id);

        if ($meal->state !== 'pending') {
            return redirect()->back()->with('error', 'This meal is not pending approval.');
        }

        $meal->update(['state' => 'rejected']);

        return redirect()->back()->with('success', 'Meal rejected.');
    }

    /**
     * AI Suggest Diets for Meal (AJAX Endpoint)
     *
     * يستقبل ID الوجبة ويرسل بياناتها لـ Gemini AI
     * ويعيد قائمة بالحميات المقترحة
     */
    public function aiSuggestDiets(string $id)
    {
        try {
            $meal = Meal::with(['ingredients'])->findOrFail($id);

            // تحضير بيانات الوجبة
            $mealData = [
                'name' => $meal->name,
                'description' => $meal->description,
            ];

            // تحضير القيم الغذائية
            $nutritionValues = [
                'calories' => $meal->calories ?? 0,
                'protein_g' => $meal->protein_g ?? 0,
                'fat_g' => $meal->fat_g ?? 0,
                'carbs_g' => $meal->carbs_g ?? 0,
            ];

            // استدعاء GeminiService
            $geminiService = new \App\Services\GeminiService;
            $suggestions = $geminiService->suggestDiets(
                $mealData,
                $meal->ingredients->toArray(),
                $nutritionValues
            );

            // جلب جميع الحميات العامة
            $allPublicDiets = \App\Models\Diet::where('is_public', true)
                ->select('diets_id', 'name', 'description')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'suggested_diet_ids' => $suggestions['suggested_diet_ids'] ?? [],
                    'reason' => $suggestions['reason'] ?? 'تم تحليل الوجبة واقتراح الحميات المناسبة',
                    'all_diets' => $allPublicDiets,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in aiSuggestDiets', [
                'meal_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all public diets (Manual Selection - No AI)
     */
    public function getDiets()
    {
        $allPublicDiets = \App\Models\Diet::where('is_public', true)
            ->select('diets_id', 'name', 'description')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'suggested_diet_ids' => [], // Empty explicitly
                'reason' => null,
                'all_diets' => $allPublicDiets,
            ],
        ]);
    }
}
