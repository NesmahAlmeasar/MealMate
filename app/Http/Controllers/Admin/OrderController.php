<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders
     */
    public function index()
    {
        $user = auth()->user();

        // Build the base query
        $query = Cart::with(['client.user', 'restaurant', 'items.meal']);

        // Check for specific scope or if user is only a manager
        $isMyRestaurantScope = request('scope') === 'my_restaurant';
        $isManagerOnly = $user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin');

        if ($isMyRestaurantScope || $isManagerOnly) {
            // Get restaurants managed by this user
            $restaurantIds = \App\Models\Restaurant::where('manager_id', $user->user_id)
                ->pluck('restaurants_id');

            // Filter orders to only those from managed restaurants
            $query->whereIn('restaurants_id', $restaurantIds);
        }

        $orders = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(string $id)
    {
        $user = auth()->user();

        $order = Cart::with(['client.user', 'restaurant', 'location', 'items.meal.category'])
            ->findOrFail($id);

        // If user is Restaurant Manager, verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $managesRestaurant = \App\Models\Restaurant::where('restaurants_id', $order->restaurants_id)
                ->where('manager_id', $user->user_id)
                ->exists();

            if (! $managesRestaurant) {
                abort(403, 'غير مصرح لك بعرض هذا الطلب');
            }
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, string $id)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'state' => 'required|string|in:pending,processing,completed,cancelled,rejected',
        ]);

        $order = Cart::findOrFail($id);

        // If user is Restaurant Manager, verify they manage this restaurant
        if ($user->hasRole('Restaurant Manager') && ! $user->hasRole('Admin')) {
            $managesRestaurant = \App\Models\Restaurant::where('restaurants_id', $order->restaurants_id)
                ->where('manager_id', $user->user_id)
                ->exists();

            if (! $managesRestaurant) {
                return redirect()->route('admin.orders.index')
                    ->withErrors(['error' => 'غير مصرح لك بتحديث هذا الطلب']);
            }
        }

        $oldStatus = $order->state;
        $order->state = $validated['state'];
        $order->save();

        // Notify Parties
        if ($oldStatus !== $validated['state']) {
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyOrderStatusChange($order, $oldStatus, $validated['state']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send order status notification: '.$e->getMessage());
            }
        }

        return redirect()->route('admin.orders.index')->with('success', 'تم تحديث حالة الطلب بنجاح!');
    }
}
