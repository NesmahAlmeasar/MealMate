<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NutritionistController extends Controller
{
    /**
     * List all nutritionists (Specialists).
     */
    public function index()
    {
        try {
            // التحقق من وجود جدول nutritionists
            $nutritionists = DB::table('users')
                ->join('nutritionists', 'users.user_id', '=', 'nutritionists.nutritionist_id')
                ->select(
                    'users.user_id as id',
                    'users.Fname',
                    'users.Lname',
                    'users.photo_url',
                    'nutritionists.description',
                    'nutritionists.Academic_level'
                )
                ->get();

            $formattedData = $nutritionists->map(function ($nutritionist) {
                return [
                    'id' => $nutritionist->id,
                    'name' => $nutritionist->Fname.' '.$nutritionist->Lname,
                    'photo_url' => $nutritionist->photo_url,
                    'description' => $nutritionist->description,
                    'academic_level' => $nutritionist->Academic_level,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الأخصائيين بنجاح',
                'count' => $formattedData->count(),
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
            ], 500);
        }
    }

    /**
     * Get details of a specific nutritionist.
     */
    public function show($id)
    {
        $nutritionist = User::join('nutritionists', 'users.user_id', '=', 'nutritionists.nutritionist_id')
            ->where('users.user_id', $id)
            ->select(
                'users.user_id',
                'users.Fname',
                'users.Lname',
                'users.photo_url',
                'nutritionists.description as bio',
                'nutritionists.Academic_level'
            )
            ->first();

        if (! $nutritionist) {
            return response()->json(['status' => 'error', 'message' => 'Nutritionist not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $nutritionist,
        ]);
    }
}
