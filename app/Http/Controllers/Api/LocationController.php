<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * API: عرض جميع مواقع المستخدم
     * GET /api/locations
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // الحصول على مواقع المستخدم
            $locations = DB::table('client_addresses')
                ->join('locations', 'client_addresses.location_id', '=', 'locations.location_id')
                ->where('client_addresses.clients_id', $user->user_id)
                ->select(
                    'locations.location_id',
                    'locations.latitude_x as latitude',
                    'locations.longitude_y as longitude',
                    'locations.description',
                    'locations.created_at'
                )
                ->orderBy('client_addresses.created_at', 'asc') // الأول هو الافتراضي
                ->get();

            // تحديد الموقع الافتراضي (الأول في القائمة)
            $locationsWithDefault = $locations->map(function ($location, $index) {
                return [
                    'location_id' => $location->location_id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'description' => $location->description,
                    'is_default' => $index === 0, // الأول افتراضي
                    'created_at' => $location->created_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب المواقع بنجاح',
                'data' => [
                    'locations' => $locationsWithDefault,
                    'count' => $locationsWithDefault->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب المواقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: إضافة موقع جديد للعميل
     * POST /api/locations
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'description' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // إنشاء الموقع
            $location = Location::create([
                'latitude_x' => $request->latitude,
                'longitude_y' => $request->longitude,
                'description' => $request->description,
            ]);

            // التحقق من وجود مواقع سابقة
            $hasLocations = DB::table('client_addresses')
                ->where('clients_id', $user->user_id)
                ->exists();

            // ربط الموقع بالمستخدم
            DB::table('client_addresses')->insert([
                'clients_id' => $user->user_id,
                'location_id' => $location->location_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم إضافة الموقع بنجاح',
                'data' => [
                    'location_id' => $location->location_id,
                    'latitude' => $location->latitude_x,
                    'longitude' => $location->longitude_y,
                    'description' => $location->description,
                    'is_default' => ! $hasLocations, // الأول يكون افتراضي
                    'created_at' => $location->created_at,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في إضافة الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: تحديث موقع
     * PUT /api/locations/{locationId}
     */
    public function update(Request $request, $locationId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'description' => 'nullable|string|max:255',
            ]);

            // التحقق من ملكية الموقع
            $locationExists = DB::table('client_addresses')
                ->where('clients_id', $user->user_id)
                ->where('location_id', $locationId)
                ->exists();

            if (! $locationExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الموقع غير موجود أو غير مرتبط بحسابك',
                ], 404);
            }

            $location = Location::find($locationId);

            if (! $location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الموقع غير موجود',
                ], 404);
            }

            // تحديث البيانات
            if ($request->has('latitude')) {
                $location->latitude_x = $request->latitude;
            }
            if ($request->has('longitude')) {
                $location->longitude_y = $request->longitude;
            }
            if ($request->has('description')) {
                $location->description = $request->description;
            }

            $location->save();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث الموقع بنجاح',
                'data' => [
                    'location_id' => $location->location_id,
                    'latitude' => $location->latitude_x,
                    'longitude' => $location->longitude_y,
                    'description' => $location->description,
                    'updated_at' => $location->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في تحديث الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: حذف موقع
     * DELETE /api/locations/{locationId}
     */
    public function destroy($locationId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // التحقق من ملكية الموقع
            $clientAddress = DB::table('client_addresses')
                ->where('clients_id', $user->user_id)
                ->where('location_id', $locationId)
                ->first();

            if (! $clientAddress) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الموقع غير موجود أو غير مرتبط بحسابك',
                ], 404);
            }

            DB::beginTransaction();

            // حذف الربط
            DB::table('client_addresses')
                ->where('clients_id', $user->user_id)
                ->where('location_id', $locationId)
                ->delete();

            // حذف الموقع نفسه إذا لم يكن مرتبط بعملاء آخرين
            $otherUsersCount = DB::table('client_addresses')
                ->where('location_id', $locationId)
                ->count();

            if ($otherUsersCount === 0) {
                Location::destroy($locationId);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الموقع بنجاح',
                'data' => [
                    'location_id' => $locationId,
                    'deleted' => true,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في حذف الموقع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
