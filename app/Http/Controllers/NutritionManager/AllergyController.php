<?php

namespace App\Http\Controllers\NutritionManager;

use App\Http\Controllers\Controller;
use App\Models\Allergy;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class AllergyController extends Controller
{
    public function index()
    {
        $allergies = Allergy::with('ingredients')->paginate(10);
        $ingredients = Ingredient::all();

        return view('nutrition-manager.allergies.index', compact('allergies', 'ingredients'));
    }

    public function create()
    {
        $ingredients = Ingredient::all();

        return view('nutrition-manager.allergies.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $messages = [
            'allergies.required' => 'اسم الحساسية مطلوب.',
            'allergies.unique' => 'هذه الحساسية موجودة مسبقاً.',
        ];

        $validatedData = $request->validate([
            'allergies' => 'required|string|max:255|unique:allergies,allergies',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
        ], $messages);

        $allergy = Allergy::create(['allergies' => $validatedData['allergies']]);

        if (isset($validatedData['ingredients'])) {
            $allergy->ingredients()->attach($validatedData['ingredients']);
        }

        return redirect()->route('nutrition-manager.allergies.index')
            ->with('success', 'تم إضافة الحساسية بنجاح.');
    }

    public function edit(Allergy $allergy)
    {
        $ingredients = Ingredient::all();
        $selectedIngredients = $allergy->ingredients->pluck('ingredients_id')->toArray();

        return view('nutrition-manager.allergies.edit', compact('allergy', 'ingredients', 'selectedIngredients'));
    }

    public function update(Request $request, Allergy $allergy)
    {
        $messages = [
            'allergies.required' => 'اسم الحساسية مطلوب.',
            'allergies.unique' => 'هذه الحساسية موجودة مسبقاً.',
        ];

        $validatedData = $request->validate([
            'allergies' => 'required|string|max:255|unique:allergies,allergies,'.$allergy->allergies_id.',allergies_id',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
        ], $messages);

        $allergy->update(['allergies' => $validatedData['allergies']]);

        if (isset($validatedData['ingredients'])) {
            $allergy->ingredients()->sync($validatedData['ingredients']);
        } else {
            $allergy->ingredients()->detach();
        }

        return redirect()->route('nutrition-manager.allergies.index')
            ->with('success', 'تم تحديث الحساسية بنجاح.');
    }

    public function destroy(Allergy $allergy)
    {
        // Check if used by clients
        if ($allergy->clients()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'لا يمكن حذف هذه الحساسية لأنها مرتبطة بملفات عملاء.']);
        }

        $allergy->ingredients()->detach();
        $allergy->delete();

        return redirect()->route('nutrition-manager.allergies.index')
            ->with('success', 'تم حذف الحساسية بنجاح.');
    }
}
