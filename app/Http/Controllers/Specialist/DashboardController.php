<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use App\Models\Meal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $specialistId = Auth::id();

        // Statistics
        $pendingMeals = Meal::where('state', 'pending')->count();
        $approvedMeals = Meal::where('state', 'approved')->count();

        // Diets created by this specialist
        $myDietsCount = Diet::where('nutritionist_id', $specialistId)->count();

        // Recent pending meals (limit 5)
        $recentPendingMeals = Meal::where('state', 'pending')
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('specialist.dashboard-2', compact(
            'pendingMeals',
            'approvedMeals',
            'myDietsCount',
            'recentPendingMeals'
        ));
    }

    public function users()
    {
        $specialistId = Auth::id();

        $consultations = \App\Models\Consultation::where('nutritionist_id', $specialistId)
            ->with([
                'client.client.bodyData',
                'client.client.lifestyle',
                'client.client.medicalRecords',
                'client.client.chronicDiseases',
                'client.client.allergies',
                'type',
                'diet',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('specialist.users', compact('consultations'));
    }
}
