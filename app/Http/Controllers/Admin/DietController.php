<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diet;

class DietController extends Controller
{
    /**
     * Display a listing of all diets (read-only for admin)
     */
    public function index()
    {
        $diets = Diet::with(['nutritionist.user', 'meals'])
            ->latest()
            ->get();

        return view('diets.index', compact('diets'));
    }

    /**
     * Display the specified diet (read-only for admin)
     */
    public function show(string $id)
    {
        $diet = Diet::with(['nutritionist.user', 'meals.category', 'restrictions'])->findOrFail($id);

        return view('diets.show', compact('diet'));
    }
}
