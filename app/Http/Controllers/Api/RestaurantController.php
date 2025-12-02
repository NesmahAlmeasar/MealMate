<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Meal;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the restaurants.
     */
    public function index()
    {
        $restaurants = Restaurant::select('restaurants_id', 'name', 'photo_url')->get();

        return response()->json([
            'status' => 'success',
            'data' => $restaurants
        ]);
    }

    /**
     * Display meals for a specific restaurant with filters.
     */
    public function meals(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Restaurant not found'
            ], 404);
        }

        // Start with meals belonging to categories of this restaurant
        // Note: The user asked to "show meals in the restaurant according to its section (category)".
        // And also "show all meals".
        // The relationship is Restaurant <-> Category <-> Meal.
        // So we get categories of the restaurant, then meals of those categories.
        
        $query = Meal::whereHas('category.restaurants', function ($q) use ($id) {
            $q->where('restaurants.restaurants_id', $id);
        });

        // Filter by Category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Diet
        if ($request->has('diet_id')) {
            $query->whereHas('diets', function ($q) use ($request) {
                $q->where('diets.diets_id', $request->diet_id);
            });
        }

        // Select specific fields as requested
        $meals = $query->select(
            'meals_id',
            'name',
            'description',
            'photo_url',
            'price',
            'state',
            'proper_time',
            'quantity_g',
            'calories',
            'protein_g',
            'fat_g',
            'carbs_g',
            'category_id'
        )->get();

        return response()->json([
            'status' => 'success',
            'data' => $meals
        ]);
    }
}
