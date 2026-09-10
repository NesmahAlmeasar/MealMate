<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    /**
     * Display a listing of all meals (for admin)
     */
    public function index(Request $request)
    {
        $query = Meal::with(['category', 'ingredients'])->latest();

        if ($request->filled('state') && in_array($request->state, ['approved', 'pending', 'rejected'])) {
            $query->where('state', $request->state);
        }

        $meals = $query->paginate(20);
        $categories = Category::all();

        return view('admin.meals.index', compact('meals', 'categories'));
    }

    /**
     * Show the form for creating a new meal
     */
    public function create()
    {
        $categories = Category::all();
        $ingredients = Ingredient::all();
        $restaurants = \App\Models\Restaurant::all(); // Pass restaurants

        return view('admin.meals.create', compact('categories', 'ingredients', 'restaurants'));
    }

    /**
     * Store a newly created meal in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0|max:100000',
            'preparation_time' => 'nullable|integer|min:1|max:300',
            'quantity_g' => 'nullable|numeric',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'category_id' => 'nullable|exists:category,category_id',
            'restaurant_id' => 'nullable|exists:restaurants,restaurants_id',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
            'ingredient_quantities' => 'nullable|array',
            'ingredient_quantities.*' => 'nullable|numeric|min:0',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('meals', 'public');
        }

        // Create the meal
        $meal = Meal::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $photoPath,
            'price' => $validated['price'],
            'state' => 'pending', // Default state for admin-created meals

            'preparation_time' => $validated['preparation_time'] ?? null,
            'quantity_g' => $validated['quantity_g'] ?? null,
            'calories' => $validated['calories'] ?? null,
            'protein_g' => $validated['protein_g'] ?? null,
            'fat_g' => $validated['fat_g'] ?? null,
            'carbs_g' => $validated['carbs_g'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        // Attach ingredients with quantities
        if (! empty($validated['ingredients'])) {
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $quantity = $request->ingredient_quantities[$index] ?? null;
                $ingredientsData[$ingredientId] = ['quantity_g' => $quantity];
            }
            $meal->ingredients()->attach($ingredientsData);
        }

        // Notify Nutrition Specialists
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyNutritionSpecialists($meal);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Illuminate\Support\Facades\Log::error('Failed to send meal notification: '.$e->getMessage());
        }

        return redirect()->route('admin.meals.index')->with('success', 'Meal created successfully and pending approval!');
    }

    /**
     * Display the specified meal
     */
    public function show(string $id)
    {
        $meal = Meal::with(['category', 'ingredients'])->findOrFail($id);

        return view('admin.meals.show', compact('meal'));
    }

    /**
     * Show the form for editing the specified meal
     */
    public function edit(string $id)
    {
        $meal = Meal::with('ingredients')->findOrFail($id);

        // التحقق من الصلاحية
        $this->authorize('update', $meal);

        $categories = Category::all();
        $ingredients = Ingredient::all();
        $restaurants = \App\Models\Restaurant::all();

        return view('admin.meals.edit', compact('meal', 'categories', 'ingredients', 'restaurants'));
    }

    /**
     * Update the specified meal in database
     */
    public function update(Request $request, string $id)
    {
        $meal = Meal::findOrFail($id);

        // التحقق من الصلاحية
        $this->authorize('update', $meal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0|max:100000',
            'preparation_time' => 'nullable|integer|min:1|max:300',
            'quantity_g' => 'nullable|numeric',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'category_id' => 'nullable|exists:category,category_id',
            'restaurant_id' => 'nullable|exists:restaurants,restaurants_id',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
            'ingredient_quantities' => 'nullable|array',
            'ingredient_quantities.*' => 'nullable|numeric|min:0',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($meal->photo_url) {
                Storage::disk('public')->delete($meal->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('meals', 'public');
        }

        // Update meal
        $meal->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $validated['photo_url'] ?? $meal->photo_url,
            'price' => $validated['price'],

            'preparation_time' => $validated['preparation_time'] ?? null,
            'quantity_g' => $validated['quantity_g'] ?? null,
            'calories' => $validated['calories'] ?? null,
            'protein_g' => $validated['protein_g'] ?? null,
            'fat_g' => $validated['fat_g'] ?? null,
            'carbs_g' => $validated['carbs_g'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'restaurant_id' => $validated['restaurant_id'] ?? null,
        ]);

        // Prepare new ingredients data with quantities
        $ingredientsData = [];
        if (! empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $quantity = $request->ingredient_quantities[$index] ?? null;
                $ingredientsData[$ingredientId] = ['quantity_g' => $quantity];
            }
        }

        // Check for changes in ingredients
        $currentIngredients = $meal->ingredients->pluck('pivot.quantity_g', 'ingredients_id')->toArray();
        $newData = [];
        foreach ($ingredientsData as $id => $data) {
            $newData[$id] = $data['quantity_g'];
        }

        $ingredientsChanged = false;
        if (count($currentIngredients) !== count($newData)) {
            $ingredientsChanged = true;
        } else {
            foreach ($newData as $id => $qty) {
                if (! array_key_exists($id, $currentIngredients)) {
                    $ingredientsChanged = true;
                    break;
                }
                // Compare quantity (float comparison for safety)
                if (abs((float) $currentIngredients[$id] - (float) $qty) > 0.0001) {
                    $ingredientsChanged = true;
                    break;
                }
            }
        }

        // Apply changes to database
        if (! empty($ingredientsData)) {
            $meal->ingredients()->sync($ingredientsData);
        } else {
            $meal->ingredients()->detach();
        }

        // If ingredients changed, revert status to pending and notify
        if ($ingredientsChanged) {
            $meal->state = 'pending';
            $meal->save();

            // Notify Nutrition Specialists
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyNutritionSpecialists($meal);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send meal notification: '.$e->getMessage());
            }
        }

        return redirect()->route('admin.meals.index')->with('success', 'Meal updated successfully!');
    }

    /**
     * Remove the specified meal from database
     */
    public function destroy(string $id)
    {
        $meal = Meal::findOrFail($id);

        // التحقق من الصلاحية
        $this->authorize('delete', $meal);

        // Delete photo if exists
        if ($meal->photo_url) {
            Storage::disk('public')->delete($meal->photo_url);
        }

        $meal->delete();

        return redirect()->route('admin.meals.index')->with('success', 'Meal deleted successfully!');
    }

    /**
     * AI Suggest Ingredients (AJAX Endpoint)
     *
     * يستقبل اسم الوجبة ووصفها ويرسلها لـ Gemini AI
     * ويعيد قائمة بالمكونات المقترحة مع الكميات
     */
    public function aiSuggestIngredients(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'nullable|numeric',
            ]);

            // جلب جميع المكونات المتاحة
            $ingredients = Ingredient::select('ingredients_id', 'name_ar')->get();

            if ($ingredients->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد مكونات متاحة في قاعدة البيانات',
                ], 400);
            }

            // استدعاء GeminiService
            $geminiService = new GeminiService;
            $suggestions = $geminiService->suggestIngredients(
                $request->name,
                $ingredients,
                $request->description,
                $request->quantity
            );

            if (empty($suggestions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتمكن الذكاء الاصطناعي من اقتراح مكونات مناسبة',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم اقتراح المكونات بنجاح',
                'data' => $suggestions,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
