<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Allergy;

class MealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['category_name' => 'Breakfast'],
            ['category_name' => 'Lunch'],
            ['category_name' => 'Dinner'],
            ['category_name' => 'Snacks'],
            ['category_name' => 'Desserts'],
            ['category_name' => 'Salads'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Allergies
        $allergies = [
            ['allergies' => 'Peanuts'],
            ['allergies' => 'Tree Nuts'],
            ['allergies' => 'Dairy'],
            ['allergies' => 'Eggs'],
            ['allergies' => 'Soy'],
            ['allergies' => 'Wheat/Gluten'],
            ['allergies' => 'Fish'],
            ['allergies' => 'Shellfish'],
        ];

        foreach ($allergies as $allergy) {
            Allergy::firstOrCreate($allergy);
        }

        // Create Ingredients
        $ingredients = [
            [
                'name_ar' => 'سلمون',
                'usda_term' => 'Salmon, Atlantic, farmed',
                'is_vegan' => false,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 206,
                'protein_g' => 22,
                'fat_g' => 13,
                'carbs_g' => 0,
            ],
            [
                'name_ar' => 'كينوا',
                'usda_term' => 'Quinoa, cooked',
                'is_vegan' => true,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 120,
                'protein_g' => 4.4,
                'fat_g' => 1.9,
                'carbs_g' => 21.3,
            ],
            [
                'name_ar' => 'دجاج',
                'usda_term' => 'Chicken breast, grilled',
                'is_vegan' => false,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 165,
                'protein_g' => 31,
                'fat_g' => 3.6,
                'carbs_g' => 0,
            ],
            [
                'name_ar' => 'أفوكادو',
                'usda_term' => 'Avocado, raw',
                'is_vegan' => true,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 160,
                'protein_g' => 2,
                'fat_g' => 15,
                'carbs_g' => 9,
            ],
            [
                'name_ar' => 'بروكلي',
                'usda_term' => 'Broccoli, cooked',
                'is_vegan' => true,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 55,
                'protein_g' => 3.7,
                'fat_g' => 0.6,
                'carbs_g' => 11,
            ],
            [
                'name_ar' => 'أرز بني',
                'usda_term' => 'Brown rice, cooked',
                'is_vegan' => true,
                'has_gluten' => false,
                'has_dairy' => false,
                'calories' => 112,
                'protein_g' => 2.6,
                'fat_g' => 0.9,
                'carbs_g' => 23.5,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }

        // Create Sample Meals
        $meals = [
            [
                'name' => 'Grilled Salmon with Quinoa',
                'description' => 'A perfect blend of omega-3 rich salmon with protein-packed quinoa',
                'price' => 15.99,
                'state' => 'approved',
                'proper_time' => 'lunch',
                'quantity_g' => 350,
                'calories' => 450,
                'protein_g' => 35,
                'fat_g' => 18,
                'carbs_g' => 40,
                'category_id' => 2, // Lunch
                'ingredients' => [
                    1 => ['quantity_g' => 200], // Salmon
                    2 => ['quantity_g' => 150], // Quinoa
                ],
            ],
            [
                'name' => 'Mediterranean Chicken Salad',
                'description' => 'Fresh grilled chicken with mixed greens and vegetables',
                'price' => 12.99,
                'state' => 'approved',
                'proper_time' => 'lunch',
                'quantity_g' => 300,
                'calories' => 320,
                'protein_g' => 30,
                'fat_g' => 15,
                'carbs_g' => 20,
                'category_id' => 6, // Salads
                'ingredients' => [
                    3 => ['quantity_g' => 150], // Chicken
                    4 => ['quantity_g' => 50],  // Avocado
                    5 => ['quantity_g' => 100], // Broccoli
                ],
            ],
            [
                'name' => 'Healthy Buddha Bowl',
                'description' => 'Nutritious bowl with quinoa, vegetables, and avocado',
                'price' => 11.99,
                'state' => 'pending',
                'proper_time' => 'dinner',
                'quantity_g' => 400,
                'calories' => 380,
                'protein_g' => 15,
                'fat_g' => 10,
                'carbs_g' => 55,
                'category_id' => 3, // Dinner
                'ingredients' => [
                    2 => ['quantity_g' => 150], // Quinoa
                    4 => ['quantity_g' => 100], // Avocado
                    5 => ['quantity_g' => 150], // Broccoli
                ],
            ],
        ];

        foreach ($meals as $mealData) {
            $ingredients = $mealData['ingredients'];
            unset($mealData['ingredients']);
            
            $meal = Meal::create($mealData);
            
            // Attach ingredients
            $meal->ingredients()->attach($ingredients);
        }

        $this->command->info('Meals, ingredients, and categories seeded successfully!');
    }
}
