<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PantryEngineService
{
    /**
     * Match available home ingredients with system meals and diets.
     *
     * @param array $ingredientNames
     * @return array
     */
    public function suggestMealsFromPantry(array $ingredientNames): array
    {
        if (empty($ingredientNames)) {
            return [];
        }

        // Clean & normalize input strings
        $normalizedIngredients = array_map(function ($item) {
            return trim(mb_strtolower($item));
        }, $ingredientNames);

        // Fetch all meals with their ingredients
        $meals = DB::table('meals')
            ->leftJoin('categories', 'meals.category_id', '=', 'categories.category_id')
            ->leftJoin('restaurants', 'meals.restaurant_id', '=', 'restaurants.restaurants_id')
            ->select(
                'meals.meal_id',
                'meals.meal_name',
                'meals.description',
                'meals.price',
                'meals.calories',
                'meals.protein_g',
                'meals.carbs_g',
                'meals.fat_g',
                'restaurants.restaurants_id',
                'restaurants.name as restaurant_name'
            )
            ->get();

        $suggestions = [];

        foreach ($meals as $meal) {
            $mealText = mb_strtolower(($meal->meal_name ?? '') . ' ' . ($meal->description ?? ''));
            $matchCount = 0;
            $matchedIngredients = [];

            foreach ($normalizedIngredients as $ingredient) {
                if (mb_strpos($mealText, $ingredient) !== false) {
                    $matchCount++;
                    $matchedIngredients[] = $ingredient;
                }
            }

            if ($matchCount > 0) {
                $matchScore = min(100, round(($matchCount / count($normalizedIngredients)) * 100));

                $suggestions[] = [
                    'meal_id' => $meal->meal_id,
                    'meal_name' => $meal->meal_name,
                    'description' => $meal->description,
                    'price' => (float)$meal->price,
                    'calories' => (int)$meal->calories,
                    'protein_g' => (float)$meal->protein_g,
                    'carbs_g' => (float)$meal->carbs_g,
                    'fat_g' => (float)$meal->fat_g,
                    'restaurant_id' => $meal->restaurants_id,
                    'restaurant_name' => $meal->restaurant_name ?? 'مطعم صحي',
                    'match_count' => $matchCount,
                    'match_score' => $matchScore,
                    'matched_ingredients' => $matchedIngredients,
                ];
            }
        }

        // Sort suggestions by match_score descending
        usort($suggestions, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return array_slice($suggestions, 0, 15);
    }
}
