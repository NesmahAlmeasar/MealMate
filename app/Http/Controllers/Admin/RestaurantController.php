<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Check for specific scope or if user is only a manager
        $isMyRestaurantScope = request('scope') === 'my_restaurant';
        $isManagerOnly = $user->hasRole('Restaurant Manager') && !$user->hasRole('Admin');

        if ($isMyRestaurantScope || $isManagerOnly) {
            // Show only managed restaurants
            // If admin clicked "My Restaurant" but has no restaurant, it will be empty (correct behavior)
            // If manager, they see theirs.
            $restaurants = Restaurant::where('manager_id', $user->user_id)
                ->withCount('meals')
                ->get();
        } else {
            // Admin default view: show all restaurants
            $restaurants = Restaurant::withCount('meals')->get();
        }

        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Restaurant Manager cannot create new restaurants
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            return redirect()->route('admin.restaurants.index')
                ->withErrors(['error' => 'غير مصرح لك بإنشاء مطاعم جديدة']);
        }

        // Get users with role 'Restaurant Manager'
        $managers = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Restaurant Manager');
        })->get();

        return view('admin.restaurants.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Restaurant Manager cannot create new restaurants
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            return redirect()->route('admin.restaurants.index')
                ->withErrors(['error' => 'غير مصرح لك بإنشاء مطاعم جديدة']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:restaurants,name',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'state' => 'required|in:active,inactive,pending',
            'manager_id' => 'nullable|exists:users,user_id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:20|regex:/^[0-9\+\-\s]+$/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_description' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ], [
            'name.required' => 'اسم المطعم مطلوب',
            'name.unique' => 'اسم المطعم موجود بالفعل، يرجى اختيار اسم آخر',
            'manager_id.exists' => 'مدير المطعم المحدد غير موجود',
        ]);

        $restaurant = new Restaurant;
        $restaurant->name = $validated['name'];
        $restaurant->description = $validated['description'] ?? null;
        $restaurant->email = $validated['email'] ?? null;
        $restaurant->state = $validated['state'] ?? 'active';
        $restaurant->manager_id = $validated['manager_id'] ?? null;
        $restaurant->commission_rate = $request->input('commission_rate', 10.00);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('restaurants', 'public');
            $restaurant->photo_url = $path;
        }

        $restaurant->save();

        // Save Phones
        if (!empty($request->phones)) {
            foreach ($request->phones as $phone) {
                if ($phone) {
                    \App\Models\Phone::create([
                        'phone_number' => $phone,
                        'restaurants_id' => $restaurant->restaurants_id,
                    ]);
                }
            }
        }

        // Save Map Location
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $location = \App\Models\Location::create([
                'latitude_x' => $request->latitude,
                'longitude_y' => $request->longitude,
                'description' => $request->location_description ?? null,
            ]);
            $restaurant->location_id = $location->location_id;
            $restaurant->save(); // Save again to update foreign key
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'تم إضافة المطعم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::with(['meals.category'])->findOrFail($id);

        // Restaurant Manager: verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            if ($restaurant->manager_id !== $user->user_id) {
                abort(403, 'غير مصرح لك بعرض هذا المطعم');
            }
        }

        // Calculate Real Performance Stats
        $totalOrders = \App\Models\Cart::where('restaurants_id', $id)
            ->where('state', 'completed')
            ->count();

        $totalSales = \App\Models\Cart::where('restaurants_id', $id)
            ->where('state', 'completed')
            ->sum('total_price');

        return view('admin.restaurants.show', compact('restaurant', 'totalOrders', 'totalSales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::findOrFail($id);

        // Restaurant Manager: verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            if ($restaurant->manager_id !== $user->user_id) {
                abort(403, 'غير مصرح لك بتعديل هذا المطعم');
            }
        }

        $managers = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Restaurant Manager');
        })->get();

        return view('admin.restaurants.edit', compact('restaurant', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::findOrFail($id);

        // Restaurant Manager: verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            if ($restaurant->manager_id !== $user->user_id) {
                return redirect()->route('admin.restaurants.index')
                    ->withErrors(['error' => 'غير مصرح لك بتعديل هذا المطعم']);
            }
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'state' => 'required|in:active,inactive,pending',
            'manager_id' => $user->hasRole('Admin') ? 'nullable|exists:users,user_id' : 'sometimes',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:20|regex:/^[0-9\+\-\s]+$/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_description' => 'nullable|string|max:255',
        ]);

        $restaurant->name = $validated['name'];
        $restaurant->description = $validated['description'] ?? null;
        $restaurant->email = $validated['email'] ?? null;
        $restaurant->state = $validated['state'];
        if ($request->has('commission_rate')) {
            $restaurant->commission_rate = $request->input('commission_rate');
        }


        // Only Admin can change manager_id
        if ($user->hasRole('Admin') && isset($validated['manager_id'])) {
            $restaurant->manager_id = $validated['manager_id'];
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($restaurant->photo_url) {
                Storage::disk('public')->delete($restaurant->photo_url);
            }
            $path = $request->file('photo')->store('restaurants', 'public');
            $restaurant->photo_url = $path;
        }

        $restaurant->save();

        // Sync Phones: Delete all old and re-add
        $restaurant->phones()->delete();
        if (!empty($request->phones)) {
            foreach ($request->phones as $phone) {
                if ($phone) {
                    \App\Models\Phone::create([
                        'phone_number' => $phone,
                        'restaurants_id' => $restaurant->restaurants_id,
                    ]);
                }
            }
        }

        // Sync Locations: Detach and re-add (Assuming locations are unique per restaurant context in this simple implementation)
        // For simplicity, we are creating new locations or finding existing.
        // In this implementation matching "create" logic: we might create duplicate location entries if address text is same.
        // A better approach for many-to-many is sync, but here locations seem tied to input text.
        // Let's detach all first.
        // Sync Map Location
        // Sync Map Location
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $location = $restaurant->location; // Use belongsTo relationship variable

            if ($location) {
                $location->update([
                    'latitude_x' => $request->latitude,
                    'longitude_y' => $request->longitude,
                    'description' => $request->location_description ?? null,
                ]);
            } else {
                $newLocation = \App\Models\Location::create([
                    'latitude_x' => $request->latitude,
                    'longitude_y' => $request->longitude,
                    'description' => $request->location_description ?? null,
                ]);
                $restaurant->location_id = $newLocation->location_id;
                $restaurant->save();
            }
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        // Only Admin can delete restaurants
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            return redirect()->route('admin.restaurants.index')
                ->withErrors(['error' => 'غير مصرح لك بحذف المطاعم']);
        }

        $restaurant = Restaurant::findOrFail($id);

        if ($restaurant->photo_url) {
            Storage::disk('public')->delete($restaurant->photo_url);
        }

        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant deleted successfully.');
    }

    /**
     * Show the form for creating a new meal for a specific restaurant.
     */
    public function createMeal(string $restaurantId)
    {
        $user = auth()->user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        // Restaurant Manager: verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            if ($restaurant->manager_id !== $user->user_id) {
                abort(403, 'غير مصرح لك بإضافة وجبات لهذا المطعم');
            }
        }

        $categories = Category::all();
        $ingredients = \App\Models\Ingredient::all();

        // Pass restaurant_id to the view to be handled
        return view('admin.meals.create', compact('restaurant', 'categories', 'ingredients'));
    }

    /**
     * Store a newly created meal for a specific restaurant.
     */
    public function storeMeal(Request $request, string $restaurantId)
    {
        $user = auth()->user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        // Restaurant Manager: verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && !$user->hasRole('Admin')) {
            if ($restaurant->manager_id !== $user->user_id) {
                return redirect()->route('admin.restaurants.index')
                    ->withErrors(['error' => 'غير مصرح لك بإضافة وجبات لهذا المطعم']);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:category,category_id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'quantity_g' => 'nullable|numeric',

            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,ingredients_id',
            'ingredient_quantities' => 'nullable|array',
        ]);

        $meal = new Meal;
        $meal->name = $validated['name'];
        $meal->description = $validated['description'] ?? null;
        $meal->price = $validated['price'];
        $meal->category_id = $validated['category_id'];
        $meal->restaurant_id = $restaurant->restaurants_id;

        // Optional nutritional info
        $meal->calories = $validated['calories'] ?? null;
        $meal->protein_g = $validated['protein_g'] ?? null;
        $meal->fat_g = $validated['fat_g'] ?? null;
        $meal->carbs_g = $validated['carbs_g'] ?? null;
        $meal->quantity_g = $validated['quantity_g'] ?? null;

        // Set state to pending for specialist approval
        $meal->state = 'pending';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('meals', 'public');
            $meal->photo_url = $path;
        }

        $meal->save();

        // Attach ingredients with quantities
        if (!empty($validated['ingredients'])) {
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $quantity = $request->ingredient_quantities[$index] ?? null;
                $ingredientsData[$ingredientId] = ['quantity_g' => $quantity];
            }
            $meal->ingredients()->attach($ingredientsData);
        }

        return redirect()->route('admin.restaurants.show', $restaurant->restaurants_id)
            ->with('success', 'Meal added successfully and is pending approval by specialist.');
    }

    /**
     * Store a new category (AJAX).
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|unique:category,category_name|max:100',
        ]);

        $category = new Category;
        $category->category_name = $validated['category_name'];
        $category->save();

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    /**
     * Request creation of a new restaurant (for Restaurant Managers).
     */
    public function requestCreation()
    {
        $user = auth()->user();

        // Security check: Only Restaurant Manager should need this
        if (!$user->hasRole('Restaurant Manager')) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        try {
            // Find all Admins
            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->get();

            // Create notification for each admin
            foreach ($admins as $admin) {
                // Using NotificationService if available or manual creation
                // Let's use NotificationService->createNotification for consistency
                app(\App\Services\NotificationService::class)->createNotification(
                    $admin->user_id,
                    'system_alert',
                    'طلب إضافة مطعم جديد',
                    "المستخدم {$user->Fname} {$user->Lname} يطلب إضافة مطعم جديد.",
                    ['requester_id' => $user->user_id]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلبك إلى الإدارة بنجاح.',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Restaurant request failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الطلب. حاول مرة أخرى.',
            ], 500);
        }
    }
}
