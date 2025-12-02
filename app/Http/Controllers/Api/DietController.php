<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use Illuminate\Http\Request;

class DietController extends Controller
{
    /**
     * Display a listing of the diets.
     * Returns name, photo_url, and a short description.
     */
    public function index()
    {
        // Select only necessary fields for the list view
        $diets = Diet::select('diets_id', 'name', 'photo_url', 'description')
            ->where('is_public', true) // Assuming we only want public diets for now, or all? User said "send from back", implies listing.
            ->get();

        // Transform if needed, e.g., to ensure full URL for photos
        $diets->transform(function ($diet) {
            return [
                'id' => $diet->diets_id,
                'name' => $diet->name,
                'photo_url' => $diet->photo_url, // Ensure this is a full URL in your storage logic or accessor
                'description' => $diet->description, // You might want to truncate this for "short description"
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $diets
        ]);
    }

    /**
     * Display the specified diet.
     * Returns all details including warning and advice.
     */
    public function show($id)
    {
        $diet = Diet::find($id);

        if (!$diet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Diet not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $diet
        ]);
    }
}
