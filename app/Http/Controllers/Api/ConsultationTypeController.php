<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultationType;

class ConsultationTypeController extends Controller
{
    public function index()
    {
        $types = ConsultationType::all();

        return response()->json([
            'status' => 'success',
            'data' => $types,
        ]);
    }
}
