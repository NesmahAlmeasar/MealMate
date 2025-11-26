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
            ->approved()
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
            ->pending()
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
                'photo_url' => $meal->photo_url ? asset('storage/' . $meal->photo_url) : null,
                'price' => $meal->price,
                'state' => $meal->state,
                'proper_time' => $meal->proper_time,
                'quantity_g' => $meal->quantity_g,
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'fat_g' => $meal->fat_g,
                'carbs_g' => $meal->carbs_g,
                'category' => $meal->category ? $meal->category->category_name : null,
                'ingredients' => $meal->ingredients->map(function($ingredient) {
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
            ]
        ]);
    }

    /**
     * Approve a pending meal
     */
    public function approve(string $id)
    {
        $meal = Meal::findOrFail($id);
        
        if ($meal->state !== 'pending') {
            return redirect()->back()->with('error', 'This meal is not pending approval.');
        }
        
        $meal->update(['state' => 'approved']);
        
        return redirect()->back()->with('success', 'Meal approved successfully!');
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
}
