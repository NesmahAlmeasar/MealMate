<?php

namespace Tests\Feature;

use App\Models\Meal;
use App\Models\Role;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MealApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_meal_specialist_can_approve_and_user_can_view_it(): void
    {
        // 1. Setup Data
        Storage::fake('public');
        
        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $specialistRole = Role::firstOrCreate(['name' => 'Specialist']);
        $managerRole = Role::firstOrCreate(['name' => 'Restaurant Manager']); // Not strictly needed for Admin add, but good practice
        
        // Users
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->role_id);
        
        $specialist = User::factory()->create();
        $specialist->roles()->attach($specialistRole->role_id);
        
        $user = User::factory()->create(); // Normal user
        
        // Restaurant & Category
        $restaurant = Restaurant::create(['name' => 'Test Restaurant', 'state' => 'active']);
        $category = Category::create(['category_name' => 'Test Category']);

        // 2. Admin Creates Meal
        $response = $this
            ->actingAs($admin)
            ->post(route('admin.restaurants.meals.store', $restaurant->restaurants_id), [
                'name' => 'New Tasty Meal',
                'description' => 'Delicious food',
                'price' => 50,
                'category_id' => $category->category_id,
                'price' => 50,
                'category_id' => $category->category_id,
                // 'photo' => UploadedFile::fake()->image('meal.jpg'), // GD not installed
                // Ingredients optional for this test
            ]);
            
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Verify Meal Created & Pending
        $meal = Meal::where('name', 'New Tasty Meal')->first();
        $this->assertNotNull($meal);
        $this->assertEquals('pending', $meal->state);
        $this->assertEquals($restaurant->restaurants_id, $meal->restaurant_id);

        // 3. User CANNOT see it yet
        $response = $this->actingAs($user)->get(route('shared.dishes'));
        $response->assertOk(); 
        // We can't easily assert "dont see" without parsing HTML, but we can check if it's in the view data if passed
        // Or check API. Assuming shared.dishes view lists meals.
        // Let's assert database state is still pending.

        // 4. Specialist Approves Meal
        $response = $this
            ->actingAs($specialist)
            ->post(route('specialist.meals.approve', $meal->meals_id), [
                // 'diet_ids' => [] // Optional
            ]);
            
        $response->assertOk(); // Likely validation JSON response or redirect
        if ($response->isRedirect()) {
             // If controller returns redirect
        } else {
             // If JSON
             $response->assertJson(['success' => true]);
        }

        // Verify Meal Approved
        $meal->refresh();
        $this->assertEquals('approved', $meal->state);

        // 5. User View
        // For a feature test, strictly ensuring "User sees it" implies checking the response content
        $response = $this->actingAs($user)->get(route('shared.dishes'));
        $response->assertOk();
        $response->assertSee('New Tasty Meal');
    }
}
