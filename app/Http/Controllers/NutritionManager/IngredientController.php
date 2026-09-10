<?php

namespace App\Http\Controllers\NutritionManager;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::paginate(10);

        return view('nutrition-manager.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('nutrition-manager.ingredients.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'name_ar.required' => 'حقل الاسم مطلوب.',
            'name_ar.unique' => 'هذا الاسم موجود مسبقاً.',
            'calories.numeric' => 'يجب أن تكون السعرات رقماً.',
            'calories.min' => 'لا يمكن أن تكون السعرات قيمة سالبة.',
            // Add other logical messages as needed
        ];

        $validatedData = $request->validate([
            'name_ar' => 'required|string|max:255|unique:ingredients,name_ar',
            'usda_term' => 'nullable|string|max:255',
            'is_vegan' => 'boolean',
            'has_gluten' => 'boolean',
            'has_dairy' => 'boolean',
            'calories' => 'nullable|numeric|min:0',
            'protein_g' => 'nullable|numeric|min:0',
            'fat_g' => 'nullable|numeric|min:0',
            'carbs_g' => 'nullable|numeric|min:0',
        ], $messages);

        Ingredient::create($validatedData);

        return redirect()->route('nutrition-manager.ingredients.index')
            ->with('success', 'تم إضافة المكون بنجاح.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('nutrition-manager.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $messages = [
            'name_ar.required' => 'حقل الاسم مطلوب.',
            'name_ar.unique' => 'هذا الاسم موجود مسبقاً.',
            'calories.numeric' => 'يجب أن تكون السعرات رقماً.',
            'calories.min' => 'لا يمكن أن تكون السعرات قيمة سالبة.',
        ];

        $validatedData = $request->validate([
            'name_ar' => 'required|string|max:255|unique:ingredients,name_ar,'.$ingredient->ingredients_id.',ingredients_id',
            'usda_term' => 'nullable|string|max:255',
            'is_vegan' => 'boolean',
            'has_gluten' => 'boolean',
            'has_dairy' => 'boolean',
            'calories' => 'nullable|numeric|min:0',
            'protein_g' => 'nullable|numeric|min:0',
            'fat_g' => 'nullable|numeric|min:0',
            'carbs_g' => 'nullable|numeric|min:0',
        ], $messages);

        // Fix checkbox handling if not present in request
        $validatedData['is_vegan'] = $request->has('is_vegan');
        $validatedData['has_gluten'] = $request->has('has_gluten');
        $validatedData['has_dairy'] = $request->has('has_dairy');

        $ingredient->update($validatedData);

        return redirect()->route('nutrition-manager.ingredients.index')
            ->with('success', 'تم تحديث المكون بنجاح.');
    }

    public function destroy(Ingredient $ingredient)
    {
        // Check if ingredient is used in any meals
        if ($ingredient->meals()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'لا يمكن حذف هذا المكون لأنه مستخدم في وجبات.']);
        }

        $ingredient->delete();

        return redirect()->route('nutrition-manager.ingredients.index')
            ->with('success', 'تم حذف المكون بنجاح.');
    }
}
