<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use App\Models\Meal;
use App\Models\Restriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DietController extends Controller
{
    /**
     * Display a listing of diets for the specialist
     */
    public function index()
    {
        $diets = Diet::with(['nutritionist.user', 'meals'])
            ->latest()
            ->get();
        
        return view('specialist.diets-main-new', compact('diets'));
    }

    /**
     * Show the form for creating a new diet
     */
    public function create()
    {
        $meals = Meal::approved()->with('category')->get();
        
        return view('specialist.diet-add-new', compact('meals'));
    }

    /**
     * Store a newly created diet in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean',
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'restrictions' => 'nullable|array',
            'restrictions.*.field_name' => 'required|string',
            'restrictions.*.operator' => 'required|string',
            'restrictions.*.value' => 'required|string',
            'restrictions.*.restriction' => 'nullable|string',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('diets', 'public');
        }

        // Get current user's nutritionist_id
        $nutritionistId = Auth::id();

        // Ensure nutritionist record exists
        $nutritionist = \App\Models\Nutritionist::firstOrCreate(
            ['nutritionist_id' => $nutritionistId],
            [
                'Academic_level' => 'Specialist',
                'description' => 'Nutrition Specialist'
            ]
        );

        // Create the diet
        $diet = Diet::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $photoPath,
            'is_public' => $request->has('is_public') ? true : false,
            'warning' => $validated['warning'] ?? null,
            'advice' => $validated['advice'] ?? null,
            'nutritionist_id' => $nutritionistId,
        ]);

        // Attach meals
        if (!empty($validated['meals'])) {
            $diet->meals()->attach($validated['meals']);
        }

        // Create restrictions
        if (!empty($validated['restrictions'])) {
            foreach ($validated['restrictions'] as $restrictionData) {
                Restriction::create([
                    'diets_id' => $diet->diets_id,
                    'field_name' => $restrictionData['field_name'],
                    'operator' => $restrictionData['operator'],
                    'value' => $restrictionData['value'],
                    'restriction' => $restrictionData['restriction'] ?? null,
                ]);
            }
        }

        return redirect()->route('specialist.diets.index')->with('success', 'Diet created successfully!');
    }

    /**
     * Display the specified diet
     */
    public function show(string $id)
    {
        $diet = Diet::with(['nutritionist.user', 'meals.category', 'restrictions'])->findOrFail($id);
        
        return view('specialist.diet-details', compact('diet'));
    }

    /**
     * Show the form for editing the specified diet
     */
    public function edit(string $id)
    {
        $diet = Diet::with(['meals', 'restrictions'])->findOrFail($id);
        $meals = Meal::approved()->with('category')->get();
        
        return view('specialist.diet-edit', compact('diet', 'meals'));
    }

    /**
     * Update the specified diet in database
     */
    public function update(Request $request, string $id)
    {
        $diet = Diet::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean',
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'restrictions' => 'nullable|array',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($diet->photo_url) {
                Storage::disk('public')->delete($diet->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('diets', 'public');
        }

        // Update diet
        $diet->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $validated['photo_url'] ?? $diet->photo_url,
            'is_public' => $request->has('is_public') ? true : false,
            'warning' => $validated['warning'] ?? null,
            'advice' => $validated['advice'] ?? null,
        ]);

        // Sync meals
        if (isset($validated['meals'])) {
            $diet->meals()->sync($validated['meals']);
        } else {
            $diet->meals()->detach();
        }

        // Update restrictions
        if (isset($validated['restrictions'])) {
            $diet->restrictions()->delete();
            foreach ($validated['restrictions'] as $restrictionData) {
                Restriction::create([
                    'diets_id' => $diet->diets_id,
                    'field_name' => $restrictionData['field_name'],
                    'operator' => $restrictionData['operator'],
                    'value' => $restrictionData['value'],
                    'restriction' => $restrictionData['restriction'] ?? null,
                ]);
            }
        }

        return redirect()->route('specialist.diets.index')->with('success', 'Diet updated successfully!');
    }

    /**
     * Remove the specified diet from database
     */
    public function destroy(string $id)
    {
        $diet = Diet::findOrFail($id);
        
        if ($diet->photo_url) {
            Storage::disk('public')->delete($diet->photo_url);
        }
        
        $diet->delete();

        return redirect()->route('specialist.diets.index')->with('success', 'Diet deleted successfully!');
    }
}
