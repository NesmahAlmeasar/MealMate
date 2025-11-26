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
        $orders = Cart::with(['client.user', 'restaurant', 'items.meal'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(string $id)
    {
        $order = Cart::with(['client.user', 'restaurant', 'items.meal.category'])
            ->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'state' => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        $order = Cart::findOrFail($id);
        $order->state = $validated['state'];
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated successfully!');
    }
}
