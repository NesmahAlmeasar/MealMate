<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Diet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietController extends Controller
{
    /**
     * Display a listing of the diets.
     * Returns public diets + user's private diets
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $client = null; // تعريف المتغير خارج الـ if

        // جلب الحميات العامة
        $publicDiets = Diet::select('diets_id', 'name', 'photo_url', 'description', 'is_public')
            ->where('is_public', true)
            ->get();

        // جلب الحميات الخاصة بالمستخدم (إذا كان مسجل دخول)
        $userPrivateDiets = collect();
        if ($user) {
            $client = Client::where('clients_id', $user->user_id)->first();
            if ($client) {
                // جلب الحميات المرتبطة بالمستخدم من جدول client_diets
                $userPrivateDiets = $client->diets()
                    ->select('diets.diets_id', 'diets.name', 'diets.photo_url', 'diets.description', 'diets.is_public')
                    ->get();
            }
        }

        // دمج الحميات العامة والخاصة
        $allDiets = $publicDiets->concat($userPrivateDiets)->unique('diets_id');

        // تنسيق البيانات
        $diets = $allDiets->map(function ($diet) use ($user, $client) {
            $isUserDiet = false;

            // التحقق إذا كانت هذه الحمية مرتبطة بالمستخدم
            if ($user && $client) {
                $isUserDiet = $client->diets()->where('diets.diets_id', $diet->diets_id)->exists();
            }

            return [
                'diets_id' => $diet->diets_id,
                'name' => $diet->name,
                'photo_url' => $diet->photo_url ? asset('storage/'.$diet->photo_url) : null,
                'description' => $diet->description,
                'is_public' => $diet->is_public,
                'is_user_diet' => $isUserDiet,
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $diets,
        ]);
    }

    /**
     * Display the specified diet.
     * Returns all details including warning and advice.
     * Checks if user has access to private diets.
     */
    public function show(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();

        $diet = Diet::with(['restrictions', 'meals'])->find($id);

        if (! $diet) {
            return response()->json([
                'status' => 'error',
                'message' => 'الحمية غير موجودة',
            ], 404);
        }

        // إذا كانت الحمية خاصة، تحقق من صلاحية الوصول
        if (! $diet->is_public) {
            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول لعرض هذه الحمية',
                ], 401);
            }

            $client = Client::where('clients_id', $user->user_id)->first();

            // التحقق من أن الحمية مرتبطة بالمستخدم
            $hasAccess = $client && $client->diets()->where('diets.diets_id', $diet->diets_id)->exists();

            if (! $hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'غير مصرح لك بعرض هذه الحمية',
                ], 403);
            }
        }

        // تنسيق البيانات
        $dietData = [
            'diets_id' => $diet->diets_id,
            'name' => $diet->name,
            'description' => $diet->description,
            'photo_url' => $diet->photo_url ? asset('storage/'.$diet->photo_url) : null,
            'warning' => $diet->warning,
            'advice' => $diet->advice,
            'is_public' => $diet->is_public,
            'restrictions' => $diet->restrictions->map(fn ($r) => [
                'field_name' => $r->field_name,
                'operator' => $r->operator,
                'value' => $r->value,
                'restriction' => $r->restriction,
            ]),
            'meals_count' => $diet->meals->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $dietData,
        ]);
    }
}
