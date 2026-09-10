<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PantryEngineService;
use Illuminate\Http\Request;

class PantryController extends Controller
{
    protected PantryEngineService $pantryEngineService;

    public function __construct(PantryEngineService $pantryEngineService)
    {
        $this->pantryEngineService = $pantryEngineService;
    }

    /**
     * Suggest meals based on pantry ingredients available at home.
     */
    public function suggestMeals(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'string|max:100',
        ]);

        $ingredients = $request->input('ingredients', []);
        $results = $this->pantryEngineService->suggestMealsFromPantry($ingredients);

        return response()->json([
            'status' => true,
            'message' => 'تم استخراج اقتراحات مطبخي الذكي بنجاح',
            'data' => [
                'input_ingredients' => $ingredients,
                'total_suggestions' => count($results),
                'suggestions' => $results,
            ],
        ]);
    }
}
