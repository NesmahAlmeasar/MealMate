<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Restaurant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::whereHas('roles', function ($q) {
            $q->where('name', 'User');
        })->count();

        $totalSpecialists = User::whereHas('roles', function ($q) {
            $q->where('name', 'Specialist');
        })->count();

        $totalMeals = Meal::count();
        $totalRestaurants = Restaurant::count();
        $pendingMeals = Meal::where('state', 'pending')->count();

        // Recent Users (last 5 registered users)
        $recentUsers = User::whereHas('roles', function ($q) {
            $q->where('name', 'User');
        })->latest()->take(5)->get();

        return view('admin.dashboard-2', compact(
            'totalUsers',
            'totalSpecialists',
            'totalMeals',
            'totalRestaurants',
            'pendingMeals',
            'recentUsers'
        ));
    }
}
