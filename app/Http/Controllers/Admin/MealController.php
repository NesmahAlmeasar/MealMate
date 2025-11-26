<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    /**
     * Display a listing of all meals (for admin)
     */
    public function index()
    {
        $meals = Meal::with(['category', 'ingredients'])->latest()->paginate(20);
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
        
        return view('admin.meals.create', compact('categories', 'ingredients'));
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
            'price' => 'required|numeric|min:0',
            'proper_time' => 'nullable|string',
            'quantity_g' => 'nullable|numeric',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'category_id' => 'nullable|exists:category,category_id',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
            'ingredient_quantities' => 'nullable|array',
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
            'proper_time' => $validated['proper_time'] ?? null,
            'quantity_g' => $validated['quantity_g'] ?? null,
            'calories' => $validated['calories'] ?? null,
            'protein_g' => $validated['protein_g'] ?? null,
            'fat_g' => $validated['fat_g'] ?? null,
            'carbs_g' => $validated['carbs_g'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        // Attach ingredients with quantities
        if (!empty($validated['ingredients'])) {
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $quantity = $request->ingredient_quantities[$index] ?? null;
                $ingredientsData[$ingredientId] = ['quantity_g' => $quantity];
            }
            $meal->ingredients()->attach($ingredientsData);
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
        $categories = Category::all();
        $ingredients = Ingredient::all();
        
        return view('admin.meals.edit', compact('meal', 'categories', 'ingredients'));
    }

    /**
     * Update the specified meal in database
     */
    public function update(Request $request, string $id)
    {
        $meal = Meal::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'proper_time' => 'nullable|string',
            'quantity_g' => 'nullable|numeric',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'category_id' => 'nullable|exists:category,category_id',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
            'ingredient_quantities' => 'nullable|array',
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
            'proper_time' => $validated['proper_time'] ?? null,
            'quantity_g' => $validated['quantity_g'] ?? null,
            'calories' => $validated['calories'] ?? null,
            'protein_g' => $validated['protein_g'] ?? null,
            'fat_g' => $validated['fat_g'] ?? null,
            'carbs_g' => $validated['carbs_g'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        // Sync ingredients
        if (isset($validated['ingredients'])) {
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $quantity = $request->ingredient_quantities[$index] ?? null;
                $ingredientsData[$ingredientId] = ['quantity_g' => $quantity];
            }
            $meal->ingredients()->sync($ingredientsData);
        } else {
            $meal->ingredients()->detach();
        }

        return redirect()->route('admin.meals.index')->with('success', 'Meal updated successfully!');
    }

    /**
     * Remove the specified meal from database
     */
    public function destroy(string $id)
    {
        $meal = Meal::findOrFail($id);
        
        // Delete photo if exists
        if ($meal->photo_url) {
            Storage::disk('public')->delete($meal->photo_url);
        }
        
        $meal->delete();

        return redirect()->route('admin.meals.index')->with('success', 'Meal deleted successfully!');
    }
}
