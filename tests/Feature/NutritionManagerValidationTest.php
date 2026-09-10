<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use Tests\TestCase;

class NutritionManagerValidationTest extends TestCase
{
    use RefreshDatabase; // Commented out to avoid wiping existing DB, will handle cleanup manually if needed or use transactions

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        if (!Role::where('name', 'Nutrition Manager')->exists()) {
            Role::create(['name' => 'Nutrition Manager', 'guard_name' => 'web']);
        }
    }

    public function test_nutrition_manager_cannot_create_duplicate_ingredient()
    {
        // 1. Create Nutrition Manager User
        $user = User::factory()->create();
        $role = Role::where('name', 'Nutrition Manager')->first();
        $user->roles()->attach($role->role_id);
        $user->refresh();
        // dump($user->roles->toArray());
        $this->assertTrue($user->hasRole('Nutrition Manager'), 'User does not have role Nutrition Manager');

        // 2. Create an existing ingredient
        $existingIngredient = Ingredient::create([
            'name_ar' => 'Test Unique Ingredient',
            'calories' => 100
        ]);

        // 3. Try to create another ingredient with the same name
        $response = $this->actingAs($user)->post(route('nutrition-manager.ingredients.store'), [
            'name_ar' => 'Test Unique Ingredient',
            'calories' => 200
        ]);

        // 4. Assert Validation Error
        $response->assertSessionHasErrors(['name_ar']);
        
        // Cleanup
        $existingIngredient->delete();
        $user->delete();
    }

    public function test_nutrition_manager_can_update_ingredient_without_unique_error()
    {
         // 1. Create Nutrition Manager User
         $user = User::factory()->create();
         $role = Role::where('name', 'Nutrition Manager')->first();
         $user->roles()->attach($role->role_id);
         $user->refresh();
 
         // 2. Create an ingredient
         $ingredient = Ingredient::create([
             'name_ar' => 'Test Update Ingredient',
             'calories' => 100
         ]);
 
         // 3. Update the SAME ingredient with the SAME name (should pass)
         $response = $this->actingAs($user)->put(route('nutrition-manager.ingredients.update', $ingredient->ingredients_id), [
             'name_ar' => 'Test Update Ingredient',
             'calories' => 150
         ]);
 
         // 4. Assert No Error and Redirect
         $response->assertSessionHasNoErrors();
         $response->assertRedirect(route('nutrition-manager.ingredients.index'));

         // Cleanup
         $ingredient->delete();
         $user->delete();
    }

    public function test_admin_cannot_add_meal_with_invalid_ingredient_quantity()
    {
        // 1. Create Admin User
        $user = User::factory()->create();
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        }
        $role = Role::where('name', 'Admin')->first();
        $user->roles()->attach($role->role_id);
        $user->refresh();

        // 2. Create an ingredient
        $ingredient = Ingredient::create([
            'name_ar' => 'Meal Ingredient Valid',
            'calories' => 50
        ]);

        // 3. Try to create a meal with negative ingredient quantity
        $response = $this->actingAs($user)->post(route('admin.meals.store'), [
            'name' => 'Test Meal Invalid Qty',
            'price' => 10,
            'ingredients' => [$ingredient->ingredients_id],
            'ingredient_quantities' => [-5] // Invalid
        ]);

        // 4. Assert Validation Error
        $response->assertSessionHasErrors(['ingredient_quantities.0']);

        // Cleanup
        $ingredient->delete();
        $user->delete();
    }
}
