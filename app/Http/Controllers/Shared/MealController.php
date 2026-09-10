<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    /**
     * Display a listing of approved meals (dishes)
     * For Restaurant Manager: only show meals from their restaurant
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Meal::with(['category', 'restaurant', 'ingredients'])
            ->where('state', 'approved');

        // Restaurant Manager: only show meals from their restaurant
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $restaurant = Restaurant::where('manager_id', $user->user_id)->first();
            if ($restaurant) {
                $query->where('restaurant_id', $restaurant->restaurants_id);
            } else {
                // No restaurant assigned, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by category if provided
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $meals = $query->latest()->get();
        $categories = Category::all();

        // Check permissions for add/edit/delete
        $canManageMeals = $user->hasRole('Admin') || $user->hasRole('Restaurant Manager');
        $userRestaurant = null;

        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $userRestaurant = Restaurant::where('manager_id', $user->user_id)->first();
        }

        return view('shared.dishes', compact('meals', 'categories', 'canManageMeals', 'userRestaurant'));
    }

    /**
     * Display the specified meal with full details
     */
    public function show(string $id)
    {
        $meal = Meal::with(['category', 'restaurant', 'ingredients'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'meal' => [
                'meals_id' => $meal->meals_id,
                'name' => $meal->name,
                'description' => $meal->description ?? 'لا يوجد وصف',
                'photo_url' => $meal->photo_url ? asset('storage/'.$meal->photo_url) : asset('images/meal_placeholder.jpg'),
                'price' => $meal->price ?? 0,
                'calories' => $meal->calories ?? 0,
                'protein_g' => $meal->protein_g ?? 0,
                'carbs_g' => $meal->carbs_g ?? 0,
                'fat_g' => $meal->fat_g ?? 0,
                'fiber_g' => $meal->fiber_g ?? 0,
                'quantity' => $meal->quantity ?? 0,

                'state' => $meal->state ?? 'pending',
                'category' => [
                    'id' => $meal->category->category_id ?? null,
                    'name' => $meal->category->category_name ?? 'غير محدد',
                ],
                'restaurant' => [
                    'id' => $meal->restaurant->restaurants_id ?? null,
                    'name' => $meal->restaurant->name ?? 'غير محدد',
                    'photo' => $meal->restaurant->photo_url ? asset('storage/'.$meal->restaurant->photo_url) : null,
                ],
                'ingredients' => $meal->ingredients->map(function ($ingredient) {
                    return [
                        'name' => $ingredient->name,
                        'quantity' => $ingredient->pivot->quantity_g ?? 0,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Show the form for creating a new meal
     * Only for Admin and Restaurant Manager
     */
    public function create()
    {
        $user = Auth::user();

        if (! $user->hasRole('Admin') && ! $user->hasRole('Restaurant Manager')) {
            abort(403, 'ليس لديك صلاحية لإضافة وجبات');
        }

        $categories = Category::all();

        // For Restaurant Manager: auto-select their restaurant
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $restaurant = Restaurant::where('manager_id', $user->user_id)->first();
            if (! $restaurant) {
                return redirect()->route('shared.dishes')->with('error', 'لم يتم العثور على مطعم مرتبط بحسابك');
            }
            $restaurants = collect([$restaurant]);
            $isRestaurantManager = true;
        } else {
            $restaurants = Restaurant::all();
            $isRestaurantManager = false;
        }

        return view('admin.meals.create', compact('categories', 'restaurants', 'isRestaurantManager'));
    }

    /**
     * Store a newly created meal
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->hasRole('Admin') && ! $user->hasRole('Restaurant Manager')) {
            abort(403, 'ليس لديك صلاحية لإضافة وجبات');
        }

        // Validate restaurant ownership for Restaurant Manager
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $restaurant = Restaurant::where('manager_id', $user->user_id)->first();
            if (! $restaurant || $request->restaurant_id != $restaurant->restaurants_id) {
                return back()->withErrors(['restaurant_id' => 'يمكنك فقط إضافة وجبات لمطعمك']);
            }
        }

        // Redirect to admin meal store
        return app('App\Http\Controllers\Admin\MealController')->store($request);
    }

    /**
     * Show the form for editing the specified meal
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $meal = Meal::findOrFail($id);

        // Check permissions
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $restaurant = Restaurant::where('manager_id', $user->user_id)->first();
            if (! $restaurant || $meal->restaurant_id != $restaurant->restaurants_id) {
                abort(403, 'يمكنك فقط تعديل وجبات مطعمك');
            }
        } elseif (! $user->hasRole('Admin')) {
            abort(403, 'ليس لديك صلاحية لتعديل الوجبات');
        }

        // Redirect to admin meal edit
        return redirect()->route('admin.meals.edit', $id);
    }

    /**
     * Update the specified meal
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $meal = Meal::findOrFail($id);

        // Check permissions
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $restaurant = Restaurant::where('manager_id', $user->user_id)->first();
            if (! $restaurant || $meal->restaurant_id != $restaurant->restaurants_id) {
                abort(403, 'يمكنك فقط تعديل وجبات مطعمك');
            }
        } elseif (! $user->hasRole('Admin')) {
            abort(403, 'ليس لديك صلاحية لتعديل الوجبات');
        }

        // Redirect to admin meal update
        return app('App\Http\Controllers\Admin\MealController')->update($request, $id);
    }

    /**
     * Remove the specified meal (Admin only)
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        // Only Admin can delete meals
        if (! $user->hasRole('Admin')) {
            abort(403, 'فقط المدير يمكنه حذف الوجبات');
        }

        $meal = Meal::findOrFail($id);

        // Delete photo if exists
        if ($meal->photo_url) {
            Storage::disk('public')->delete($meal->photo_url);
        }

        $meal->delete();

        return redirect()->route('shared.dishes')->with('success', 'تم حذف الوجبة بنجاح');
    }
}
