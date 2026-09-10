<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChronicDisease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChronicDiseaseController extends Controller
{
    /**
     * API 1: عرض جميع الأمراض المزمنة
     * GET /api/chronic-diseases
     */
    /**
     * API 1: عرض جميع الأمراض المزمنة (ID واسم فقط)
     * GET /api/chronic-diseases
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // تحديد الأعمدة المطلوبة فقط وتغيير اسمائها لتكون أوضح
            $query = ChronicDisease::select(
                'chronic_diseases_id as id',
                'chronic_diseases as name'
            );

            // إضافة البحث (اختياري، في حال أردت البحث لاحقاً)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('chronic_diseases', 'LIKE', "%{$search}%");
            }

            // جلب البيانات (استخدم get لجلب الكل بدون تقسيم صفحات إذا كانت القائمة للقوائم المنسدلة)
            $diseases = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب قائمة الأمراض بنجاح',
                'data' => $diseases,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // Methods search, show, store, update, destroy removed as requested.

    /**
     * API 7: أمراض مستخدم معين
     * GET /api/chronic-diseases/client/{clientId}
     */
    public function clientDiseases($clientId): JsonResponse
    {
        try {
            $diseases = ChronicDisease::whereHas('clients', function ($query) use ($clientId) {
                $query->where('clients_id', $clientId);
            })
                ->select('chronic_diseases_id as id', 'chronic_diseases as name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب أمراض المستخدم بنجاح',
                'data' => [
                    'client_id' => $clientId,
                    'diseases' => $diseases,
                    'count' => $diseases->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 8: إضافة مرض لمستخدم
     * POST /api/chronic-diseases/client/{clientId}/add
     */
    public function addClientDisease(Request $request, $clientId): JsonResponse
    {
        try {
            $request->validate([
                'disease_ids' => 'required|array',
                'disease_ids.*' => 'integer|exists:chronic_diseases,chronic_diseases_id',
            ]);

            // هنا يجب التحقق من صلاحيات المستخدم
            // يمكنك إضافة middleware أو تحقق يدوي

            foreach ($request->disease_ids as $diseaseId) {
                DB::table('clients_chronic_diseases')->updateOrInsert(
                    [
                        'clients_id' => $clientId,
                        'chronic_diseases_id' => $diseaseId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم إضافة الأمراض للمستخدم بنجاح',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في إضافة الأمراض',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
